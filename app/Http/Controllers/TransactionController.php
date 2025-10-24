<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Helpers\StockHelper;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index($type, $purchaseType)
    {
        $transactions = Transaction::latest()->paginate(20);
        return view('pages.transactions.index', compact('transactions', "type", "purchaseType"));
    }

    public function create($type, $purchaseType)
    {
        $variants = \App\Models\ProductVariant::with(['product','karat'])
        ->orderBy('id', 'desc')
        ->get()
        ->map(function($v){
            return [
                'id' => $v->id,
                'label' => ($v->product->name ?? 'Produk') 
                          . ' - ' . ($v->karat->name ?? '-') 
                          . ' - ' . ($v->gram ?? '-') . 'g',
                'product_name' => $v->product->name ?? null,
                'karat_name'   => $v->karat->name ?? null,
                'gram'         => $v->gram,
            ];
        });
        return view('pages.transactions.create', compact(["type","purchaseType", "variants"]));
    }

    public function store($type, $purchaseType, Request $request){
        $data = $request->all();

        // Decode JSON details (dari form JS)
        if (isset($data['details']) && is_string($data['details'])) {
            $decoded = json_decode($data['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['details' => 'Format detail tidak valid.']);
            }
            $data['details'] = $decoded;
        }

        // Validasi input
        $validator = \Validator::make($data, [
            'invoice_number' => 'nullable|string|max:255',
            'customer_name'  => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
            'details'        => 'required|array|min:1',
            'details.*.product_name'   => 'required|string|max:255',
            'details.*.karat_name'     => 'required|string|max:100',
            'details.*.gram'           => 'required|numeric|min:0.001',
            'details.*.qty'            => 'required|numeric|min:0.001',
            'details.*.price_per_gram' => 'required|numeric|min:0',
            'details.*.subtotal'       => 'nullable|numeric|min:0',
        ], [
            'details.required' => 'Minimal satu barang harus diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($validated, $type, $purchaseType) {
                // Buat header transaksi
                $transaction = \App\Models\Transaction::create([
                    'type' => $type,
                    'branch_id' => 2,
                    'storage_location_id' => 1,
                    'purchase_type' => $purchaseType,
                    'transaction_date' => now(),
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'note' => $validated['note'] ?? null,
                    'created_by' => auth()->id(),
                    'total' => 0,
                ]);

                $total = 0;

                foreach ($validated['details'] as $detail) {
                    $productName = trim($detail['product_name']);
                    $karatName   = trim($detail['karat_name']);
                    $gram        = (float) $detail['gram'];
                    $qty         = (float) $detail['qty'];
                    $price       = (float) $detail['price_per_gram'];

                    // Pastikan product & karat ada
                    $product = \App\Models\Product::firstOrCreate(
                        ['name' => $productName],
                        ['code' => \Str::slug($productName)]
                    );

                    $karat = \App\Models\Karat::firstOrCreate(['name' => $karatName]);

                    // Hitung subtotal
                    $subtotal = $gram * $price * $qty;
                    $total += $subtotal;

                    // Simpan detail transaksi
                    \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'karat_id'       => $karat->id,
                        'gram'           => $gram,
                        'qty'            => $qty,
                        'unit_price'     => $price,
                        'subtotal'       => $subtotal,
                        'type'       => $purchaseType == 'sepuh'? 'sepuh': ($transaction->purchase_type  == 'pabrik' ? 'new' : 'rosok'),
                        'note'           => $detail['note'] ?? null,
                    ]);

                    // Catat pergerakan stok
                    $movementType = $transaction->type === 'purchase' ? 'in' : 'out';
                    \App\Helpers\StockHelper::moveStock(
                        $product->id,
                        $karat->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        $movementType,
                        $qty,
                        $gram,
                        'Transaction',
                        $transaction->id,
                        'create-transaction',
                        auth()->id()
                    );
                }

                // Update total transaksi
                $transaction->update(['total' => $total]);
            });

            return redirect()
                ->route('transaksi.index', ["type" => $type, "purchaseType" => $purchaseType])
                ->with('status', 'saved');
        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['msg' => 'Transaksi gagal: ' . $e->getMessage()]);
        }
    }


   public function edit($type, $purchaseType, Transaction $transaction)
    {
        // load relasi product & karat pada details
        $transaction->load('details.product', 'details.karat');

        // ambil daftar products & karats untuk dropdown (kirim sebagai array nama)
        $products = \App\Models\Product::orderBy('name')->get()->map(function($p){
            return $p->name;
        })->values()->toArray();

        $karats = \App\Models\Karat::orderBy('name')->get()->map(function($k){
            return $k->name;
        })->values()->toArray();

        // existing details untuk diisi ke form (array biasa)
        $existingDetails = $transaction->details->map(function ($d) {
            return [
                'product_name'   => $d->product->name ?? '',
                'karat_name'     => $d->karat->name ?? '',
                'gram'           => $d->gram,
                'qty'            => $d->qty,
                'price_per_gram' => $d->unit_price,
                'subtotal'       => $d->subtotal,
                'note'           => $d->note,
            ];
        })->values()->toArray();

        return view('pages.transactions.edit', [
            'transaction'  => $transaction,
            'products'     => $products,
            'karats'       => $karats,
            'details'      => $existingDetails, // untuk @json di blade
            'type'         => $type,
            'purchaseType' => $purchaseType,
            'pageTitle'    => 'Edit Transaksi ' . ucfirst($type),
            'submitUrl'    => route('transaksi.update', [
                'type' => $type, 'purchaseType' => $purchaseType, 'id' => $transaction->id
            ]),
            'isEdit'       => true,
        ]);
    }




    public function update($type, $purchaseType, $id, Request $request){
        $data = $request->all();

        // Decode JSON details
        if (isset($data['details']) && is_string($data['details'])) {
            $decoded = json_decode($data['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withInput()->withErrors(['details' => 'Format detail tidak valid.']);
            }
            $data['details'] = $decoded;
        }

        // Validasi input
        $validator = \Validator::make($data, [
            'invoice_number' => 'nullable|string|max:255',
            'customer_name'  => 'nullable|string|max:255',
            'note'           => 'nullable|string|max:1000',
            'details'        => 'required|array|min:1',
            'details.*.product_name'   => 'required|string|max:255',
            'details.*.karat_name'     => 'required|string|max:100',
            'details.*.gram'           => 'required|numeric|min:0.001',
            'details.*.qty'            => 'required|numeric|min:0.001',
            'details.*.price_per_gram' => 'required|numeric|min:0',
            'details.*.subtotal'       => 'nullable|numeric|min:0',
        ], [
            'details.required' => 'Minimal satu barang harus diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($id, $validated, $type, $purchaseType) {
                $transaction = \App\Models\Transaction::findOrFail($id);

                // 1️⃣ Rollback stok lama (seperti destroy)
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_id,
                        $detail->karat_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        // arah kebalikan (jika purchase = in → rollback = out)
                        $transaction->type === 'purchase' ? 'out' : 'in',
                        $detail->qty,
                        $detail->gram,
                        'Transaction',
                        $transaction->id,
                        'rollback-before-update',
                        auth()->id()
                    );
                }

                // 2️⃣ Hapus semua detail lama (tapi tidak hapus movement)
                $transaction->details()->delete();

                // 3️⃣ Update header transaksi
                $transaction->update([
                    'invoice_number' => $validated['invoice_number'] ?? null,
                    'customer_name'  => $validated['customer_name'] ?? null,
                    'note'           => $validated['note'] ?? null,
                    'purchase_type'  => $purchaseType,
                    'type'           => $type,
                    'updated_by'     => auth()->id(),
                    'transaction_date' => now(),
                ]);

                // 4️⃣ Tambah ulang detail baru & catat stok baru
                $total = 0;
                foreach ($validated['details'] as $detail) {
                    $productName = trim($detail['product_name']);
                    $karatName   = trim($detail['karat_name']);
                    $gram        = (float) $detail['gram'];
                    $qty         = (float) $detail['qty'];
                    $price       = (float) $detail['price_per_gram'];

                    $product = \App\Models\Product::firstOrCreate(
                        ['name' => $productName],
                        ['code' => \Str::slug($productName)]
                    );

                    $karat = \App\Models\Karat::firstOrCreate(['name' => $karatName]);

                    $subtotal = $gram * $price * $qty;
                    $total += $subtotal;

                    \App\Models\TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id'     => $product->id,
                        'karat_id'       => $karat->id,
                        'gram'           => $gram,
                        'qty'            => $qty,
                        'unit_price'     => $price,
                        'subtotal'       => $subtotal,
                        'type'           => $purchaseType == 'sepuh'
                            ? 'sepuh'
                            : ($purchaseType == 'pabrik' ? 'new' : 'rosok'),
                        'note'           => $detail['note'] ?? null,
                    ]);

                    // Catat pergerakan stok baru
                    $movementType = $transaction->type === 'purchase' ? 'in' : 'out';
                    \App\Helpers\StockHelper::moveStock(
                        $product->id,
                        $karat->id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        $movementType,
                        $qty,
                        $gram,
                        'Transaction',
                        $transaction->id,
                        'updated',
                        auth()->id()
                    );
                }

                // 5️⃣ Update total transaksi
                $transaction->update(['total' => $total]);
            });

            return redirect()
                ->route('transaksi.index', ['type' => $type, 'purchaseType' => $purchaseType])
                ->with('status', 'updated');
        } catch (\Throwable $e) {
            \Log::error('Gagal update transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['msg' => 'Update transaksi gagal: ' . $e->getMessage()]);
        }
    }



    public function destroy($type, $purchaseType,Transaction $transaction){
        try {
            DB::transaction(function () use ($transaction) {
                // rollback semua stok
                foreach ($transaction->details as $detail) {
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_id,
                        $detail->karat_id,
                        $transaction->branch_id,
                        $transaction->storage_location_id,
                        'out',
                        $detail->qty,
                        $detail->variant?->gram ?? null,
                        'Transaction',
                        $transaction->id,
                        "deleted"
                    );
                }

                // hapus detail
                $transaction->details()->delete();

                // hapus header
                $transaction->delete();
            });

            return redirect()->route('transaksi.index', ['type' => $type, 'purchaseType' => $purchaseType])->with('status', 'deleted');
        } catch (\Throwable $e) {
            \Log::error('Gagal hapus transaksi', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['msg' => 'Hapus gagal: ' . $e->getMessage()]);
        }
    }




}
