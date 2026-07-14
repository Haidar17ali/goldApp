<?php

namespace App\Http\Controllers;

use App\Helpers\AccountingHelper;
use App\Helpers\GoldHelper;
use App\Helpers\StockHelper;
use Illuminate\Http\Request;
use App\Models\TransferStock;
use App\Models\Branch;
use App\Models\Journal;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\TransferStockDetail;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferStockController extends Controller
{


    public function index(Request $request)
    {
        $query = TransferStock::with([
            'fromBranch',
            'toBranch',
            'user'
        ]);

        // ========================
        // Filter Tanggal
        // ========================
        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        // ========================
        // Dari Cabang
        // ========================
        if ($request->filled('from_branch_id')) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        // ========================
        // Ke Cabang
        // ========================
        if ($request->filled('to_branch_id')) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        // ========================
        // Status
        // ========================
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ========================
        // Keyword
        // ========================
        if ($request->filled('keyword')) {

            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {

                $q->whereHas('fromBranch', function ($x) use ($keyword) {
                    $x->where('name', 'like', "%{$keyword}%");
                })

                    ->orWhereHas('toBranch', function ($x) use ($keyword) {
                        $x->where('name', 'like', "%{$keyword}%");
                    })

                    ->orWhereHas('user', function ($x) use ($keyword) {
                        $x->where('name', 'like', "%{$keyword}%");
                    });
            });
        }

        $transfers = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $branches = Branch::orderBy('name')->get();

        return view('pages.transfer-stock.index', compact(
            'transfers',
            'branches'
        ));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();

        // hanya tampilkan variant yang memiliki stok
        $productVariants = ProductVariant::with([
            'product',
            'karat',
            'stocks'
        ])
            ->whereHas('stocks', function ($q) {
                $q->where('quantity', '>', 0)
                    ->orWhere('weight', '>', 0);
            })
            ->orderBy('product_id')
            ->get();

        return view('pages.transfer-stock.create', compact(
            'branches',
            'productVariants'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'transfer_date' => 'required|date',

            'from_branch_id' => 'required|exists:branches,id',

            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',

            'note' => 'nullable|string',

            'details' => 'required|array|min:1',

            'details.*.product_variant_id' => 'required|exists:product_variants,id',

            'details.*.qty' => 'required|integer|min:1',

        ]);

        DB::beginTransaction();

        try {

            $transfer = TransferStock::create([

                'transfer_date' => $validated['transfer_date'],

                'from_branch_id' => $validated['from_branch_id'],

                'to_branch_id' => $validated['to_branch_id'],

                'note' => $validated['note'] ?? null,

                'status' => 'draft',

                'created_by' => auth()->id(),

            ]);

            $this->applyTransfer(
                $transfer,
                $validated
            );

            DB::commit();

            return response()->json([

                'success' => true,

                'redirect' => route('mutasi-stok.index'),

                'message' => 'Mutasi berhasil disimpan.'

            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

            ], 500);
        }
    }

    private function applyTransfer(
        TransferStock $transfer,
        array $validated
    ): void {

        /*
    |--------------------------------------------------------------------------
    | Mapping COA
    |--------------------------------------------------------------------------
    */

        $persediaanAccounts = [

            1 => '103.01.001',
            2 => '103.01.002',
            3 => '103.01.003',
            4 => '103.03.01',

        ];

        $fromAccount = $persediaanAccounts[$validated['from_branch_id']]
            ?? '103.00.000';

        $toAccount = $persediaanAccounts[$validated['to_branch_id']]
            ?? '103.00.000';

        $totalNilai = 0;

        foreach ($validated['details'] as $detail) {

            $variant = ProductVariant::with([
                'product',
                'karat'
            ])->findOrFail(
                $detail['product_variant_id']
            );

            /*
        |--------------------------------------------------------------------------
        | Cari stok cabang asal
        |--------------------------------------------------------------------------
        */

            $stock = Stock::where('branch_id', $validated['from_branch_id'])
                ->where('storage_location_id', 1)
                ->where('product_variant_id', $variant->id)
                ->where('type', $variant->type)
                ->lockForUpdate()
                ->first();

            if (!$stock) {

                throw new Exception(
                    "{$variant->barcode} tidak memiliki stok."
                );
            }

            /*
        |--------------------------------------------------------------------------
        | Validasi stok
        |--------------------------------------------------------------------------
        */

            if ($variant->product->name == 'emas') {

                if ($stock->weight < ($variant->gram * $detail['qty'])) {

                    throw new Exception(
                        "Stok {$variant->barcode} tidak mencukupi."
                    );
                }
            } else {

                if ($stock->quantity < $detail['qty']) {

                    throw new Exception(
                        "Stok {$variant->barcode} tidak mencukupi."
                    );
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Simpan Detail
        |--------------------------------------------------------------------------
        */

            TransferStockDetail::create([

                'transfer_stock_id' => $transfer->id,

                'product_variant_id' => $variant->id,

                'qty' => $detail['qty'],

            ]);

            /*
        |--------------------------------------------------------------------------
        | Hitung nilai mutasi
        |--------------------------------------------------------------------------
        */

            $harga = GoldHelper::getHargaByKarat(
                $variant->karat_id
            );

            $nilai = $harga
                * $variant->gram
                * $detail['qty'];

            $totalNilai += $nilai;

            /*
        |--------------------------------------------------------------------------
        | Stock OUT
        |--------------------------------------------------------------------------
        */

            StockHelper::stockOut(

                variantId: $variant->id,

                branchId: $validated['from_branch_id'],

                storageLocationId: 1,

                quantity: $detail['qty'],

                weight: $variant->gram * $detail['qty'],

                refType: 'TransferStock',

                refId: $transfer->id,

                note: 'Mutasi ke cabang',

                userId: auth()->id(),

                goldType: $variant->type

            );

            /*
        |--------------------------------------------------------------------------
        | Stock IN
        |--------------------------------------------------------------------------
        */

            StockHelper::stockIn(

                variantId: $variant->id,

                branchId: $validated['to_branch_id'],

                storageLocationId: 1,

                quantity: $detail['qty'],

                weight: $variant->gram * $detail['qty'],

                refType: 'TransferStock',

                refId: $transfer->id,

                note: 'Mutasi dari cabang',

                userId: auth()->id(),

                goldType: $variant->type

            );
        }

        /*
    |--------------------------------------------------------------------------
    | Posting Jurnal
    |--------------------------------------------------------------------------
    */

        AccountingHelper::post([

            'date' => $transfer->transfer_date,

            'reference' => 'TRF-' . str_pad(
                $transfer->id,
                6,
                '0',
                STR_PAD_LEFT
            ),

            'description' => 'Mutasi stok antar cabang',

            'source_type' => 'TransferStock',

            'source_id' => $transfer->id,

            'lines' => [

                [

                    'account' => $toAccount,

                    'debit' => $totalNilai,

                    'description' => 'Persediaan masuk cabang tujuan',

                ],

                [

                    'account' => $fromAccount,

                    'credit' => $totalNilai,

                    'description' => 'Persediaan keluar cabang asal',

                ]

            ]

        ]);
    }

    private function rollbackTransfer(TransferStock $transfer): void
    {
        $transfer->load([
            'details.productVariant',
            'details.productVariant.product',
            'details.productVariant.karat',
        ]);

        foreach ($transfer->details as $detail) {

            $variant = $detail->productVariant;

            /*
        |--------------------------------------------------------------------------
        | Kembalikan stok ke cabang asal
        |--------------------------------------------------------------------------
        */

            StockHelper::stockIn(

                variantId: $variant->id,

                branchId: $transfer->from_branch_id,

                storageLocationId: 1,

                quantity: $detail->qty,

                weight: $variant->gram * $detail->qty,

                refType: 'TransferStockRollback',

                refId: $transfer->id,

                note: 'Rollback Mutasi Antar Cabang',

                userId: auth()->id(),

                goldType: $variant->type

            );

            /*
        |--------------------------------------------------------------------------
        | Kurangi stok cabang tujuan
        |--------------------------------------------------------------------------
        */

            StockHelper::stockOut(

                variantId: $variant->id,

                branchId: $transfer->to_branch_id,

                storageLocationId: 1,

                quantity: $detail->qty,

                weight: $variant->gram * $detail->qty,

                refType: 'TransferStockRollback',

                refId: $transfer->id,

                note: 'Rollback Mutasi Antar Cabang',

                userId: auth()->id(),

                goldType: $variant->type

            );
        }

        /*
    |--------------------------------------------------------------------------
    | Reverse Journal
    |--------------------------------------------------------------------------
    */

        $journal = Journal::with('items')
            ->where('source_type', 'TransferStock')
            ->where('source_id', $transfer->id)
            ->latest()
            ->first();

        if ($journal) {

            AccountingHelper::reverse(
                $journal,
                'Rollback Edit Mutasi Antar Cabang'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Hapus Detail Lama
    |--------------------------------------------------------------------------
    */

        $transfer->details()->delete();
    }

    public function edit($id)
    {
        $mutasi = TransferStock::findOrFail($id);
        $mutasi->load('details.productVariant');

        $branches = Branch::orderBy('name')->get();

        $productVariants = ProductVariant::with([
            'product',
            'karat',
            'stocks'
        ])->orderBy('product_id')->get();

        $oldTransferQty = $mutasi->details
            ->pluck('qty', 'product_variant_id');

        return view(
            'pages.transfer-stock.edit',
            compact(
                'mutasi',
                'branches',
                'productVariants',
                'oldTransferQty'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $mutasi = TransferStock::findOrFail($id);
        $validated = $request->validate([

            'transfer_date' => 'required|date',

            'from_branch_id' => 'required|exists:branches,id',

            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',

            'note' => 'nullable|string',

            'details' => 'required|array|min:1',

            'details.*.product_variant_id' => 'required|exists:product_variants,id',

            'details.*.qty' => 'required|integer|min:1',

        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | Rollback transaksi lama
        |--------------------------------------------------------------------------
        */

            $this->rollbackTransfer($mutasi);

            /*
        |--------------------------------------------------------------------------
        | Update Header
        |--------------------------------------------------------------------------
        */

            $mutasi->update([

                'transfer_date' => $validated['transfer_date'],

                'from_branch_id' => $validated['from_branch_id'],

                'to_branch_id' => $validated['to_branch_id'],

                'note' => $validated['note'] ?? null,

                'edited_by' => auth()->id(),

            ]);

            /*
        |--------------------------------------------------------------------------
        | Apply transaksi baru
        |--------------------------------------------------------------------------
        */

            $this->applyTransfer(
                $mutasi,
                $validated
            );

            DB::commit();

            return response()->json([

                'success' => true,

                'redirect' => route('mutasi-stok.index'),

                'message' => 'Mutasi berhasil diperbarui.'

            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

            ], 500);
        }
    }

    public function destroy($id)
    {
        $mutasi = TransferStock::findOrFail($id);
        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | Rollback stok & jurnal
        |--------------------------------------------------------------------------
        */

            $this->rollbackTransfer($mutasi);

            /*
        |--------------------------------------------------------------------------
        | Hapus Header
        |--------------------------------------------------------------------------
        */

            $mutasi->delete();

            DB::commit();

            return response()->json([

                'success' => true,

                'message' => 'Mutasi berhasil dihapus.'

            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            \Log::error($e);

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

            ], 500);
        }
    }
}
