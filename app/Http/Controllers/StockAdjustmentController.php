<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Helpers\StockHelper;
use App\Models\Branch;
use App\Models\Karat;
use App\Models\Product;
use App\Models\StorageLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StockOpnameImport;
use App\Models\ProductVariant;

class StockAdjustmentController extends BaseController
{
    public function index()
    {
        return view('pages.stock-adjustments.index');
    }

    public function create(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $locationId = $request->query('location_id');

        $stocks = Stock::where('branch_id', $branchId)
            ->where('storage_location_id', $locationId)
            ->with(['product', 'karat'])
            ->get();

        $products = Product::all();
        $karats = Karat::all();

        return view('pages.stock-adjustments.create', compact('stocks', 'branchId', 'locationId', 'products', 'karats'));
    }

    public function getStock(Request $request)
    {
        $productVariant = ProductVariant::where("product_id", $request->product_id)->where("karat_id", $request->karat_id)->where("gram", $request->weight)->where("type", $request->gold_type)->first();

        $stock = \App\Models\Stock::where([
            'product_variant_id' => $productVariant->id,
        ])->first();

        return response()->json([
            'system_qty' => $stock ? $stock->quantity : 0
        ]);
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'details' => 'required|array',
            'details.*.product_id' => 'required',
            'details.*.karat_id' => 'required',
            'details.*.gold_type' => 'required|string',
            'details.*.actual_qty' => 'required|numeric|min:0',
        ]);


        DB::transaction(function () use ($request) {

            /* ===============================
            * 1️⃣ BUAT HEADER ADJUSTMENT
            * =============================== */
            $adjustment = StockAdjustment::create([
                'branch_id' => auth()->user()->branch_id ?? 1,
                'storage_location_id' => 1,
                'adjustment_date' => now(),
                'note' => 'Manual Stock Opname',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->details as $item) {

                if (empty($item['product_id']) || $item['actual_qty'] === null) {
                    continue;
                }

                $productValue = trim($item['product_id']);
                $karatValue   = trim($item['karat_id'] ?? null);

                $type      = strtolower(trim($item['gold_type']));
                $actualQty = (int) $item['actual_qty'];
                $weight    = isset($item['weight']) ? (float) $item['weight'] : null;

                if ($actualQty < 0) continue;

                /* ===============================
                * 1️⃣ RESOLVE PRODUCT NAME
                * =============================== */
                if (is_numeric($productValue)) {
                    $product = Product::findOrFail($productValue);
                    $productName = $product->name;
                } else {
                    // dari select2 tags (nama baru)
                    $productName = $productValue;
                }

                /* ===============================
                * 2️⃣ RESOLVE KARAT NAME
                * =============================== */
                if ($karatValue && is_numeric($karatValue)) {
                    $karat = Karat::findOrFail($karatValue);
                    $karatName = $karat->name;
                } else {
                    // bisa null atau string baru
                    $karatName = $karatValue;
                }

                /* ===============================
                * 2️⃣ PASTIKAN VARIANT ADA
                * =============================== */
                $variant = StockHelper::ensureVariant(
                    $productName,
                    $karatName,
                    $weight,
                    $type
                );

                /* ===============================
                * 3️⃣ AMBIL STOK SISTEM
                * =============================== */
                $stock = Stock::where([
                    'product_variant_id' => $variant->id,
                    'branch_id' => 1,
                    'storage_location_id' => 1,
                    'type' => $type,
                ])->first();
                dd($stock);

                $systemQty = $stock?->quantity ?? 0;
                $difference = $actualQty - $systemQty;

                /* ===============================
                 * 4️⃣ SIMPAN DETAIL OPNAME
                 * =============================== */
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_variant_id' => $variant->id,
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'type' => $type,
                ]);

                /* ===============================
                 * 5️⃣ CATAT STOCK MOVEMENT
                 * =============================== */
                StockHelper::moveStock(
                    product_variant_id: $variant->id,
                    branchId: $adjustment->branch_id,
                    storageLocationId: $adjustment->storage_location_id,
                    type: 'adjustment',
                    quantity: $actualQty,
                    weight: $weight,
                    referenceType: StockAdjustment::class,
                    referenceId: $adjustment->id,
                    note: 'Manual Stock Opname',
                    userId: auth()->id(),
                    goldType: $type
                );
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Stock opname berhasil disimpan');
    }

    public function importForm()
    {
        return view('pages.stock-adjustments.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new StockOpnameImport, $request->file('file'));
            return redirect()->route('opname.index')->with('status', 'saved');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $adjustment = StockAdjustment::with('details.productVariant')->findOrFail($id);

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->details as $detail) {
                $difference = $detail->difference;
                $weight = $detail->productVariant?->gram ?? 0;

                if ($difference != 0) {
                    $typeMove = $difference > 0 ? 'out' : 'in';
                    // rollback stok
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_variant_id,
                        // $detail->karat_id,
                        $adjustment->branch_id,
                        $adjustment->storage_location_id,
                        $typeMove,
                        abs($difference),
                        $weight,
                        'StockAdjustmentCancel',
                        $adjustment->id,
                        'Rollback stock opname',
                        auth()->id(),
                        $detail->type
                    );
                }
            }

            // Hapus semua StockMovement yang referensi ke StockAdjustment ini
            \App\Models\StockMovement::where('reference_type', 'StockAdjustment')
                ->where('reference_id', $adjustment->id)
                ->delete();

            // Hapus detail
            $adjustment->details()->delete();

            // Hapus opname
            $adjustment->delete();
        });

        return redirect()->route('opname.index')->with('status', 'deleted');
    }
}
