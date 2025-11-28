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

class StockAdjustmentController extends Controller
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

    public function getStock(Request $request){
        $stock = \App\Models\Stock::where([
            'product_id' => $request->product_id,
            'karat_id' => $request->karat_id,
            'weight' => $request->weight,
            'type' => $request->gold_type,
            ])->first();

        return response()->json([
            'system_qty' => $stock ? $stock->quantity : 0
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            // 'branch_id' => 'required|integer',
            // 'storage_location_id' => 'required|integer',
            'details' => 'required|array|min:1',
            'details.*.stock_id' => 'nullable|integer',
            'details.*.product_id' => 'required|integer',
            'details.*.karat_id' => 'required|integer',
            'details.*.system_qty' => 'required|numeric',
            'details.*.weight' => 'required|numeric|min:0.01',
            'details.*.actual_qty' => 'required|numeric',
            'details.*.gold_type' => 'nullable|string|in:new,sepuh,rosok',
        ]);

        DB::transaction(function () use ($data) {
            $adjustment = StockAdjustment::create([
                'branch_id' => 2,
                'storage_location_id' => 1,
                'adjustment_date' => now(),
                'created_by' => auth()->id(),
                'note' => 'Stock opname ' . now()->format('d-m-Y'),
            ]);

            foreach ($data['details'] as$detail) {
                $systemQty = (float)$detail['system_qty'];
                $actualQty = (float)$detail['actual_qty'];
                $difference = $actualQty - $systemQty;
                $weight = isset($detail['weight']) ? (float)$detail['weight'] : null; // gram per unit (atau total berat tergantung UI)

                // simpan detail
                $detailModel = StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $detail['product_id'],
                    'karat_id' => $detail['karat_id'],
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'type' => $detail['gold_type'] ?? 'new',
                    'weight' => $weight ?? 0,
                ]);
                
                if ($difference != 0) {
                    $typeMove = $difference > 0 ? 'in' : 'out';
                    \App\Helpers\StockHelper::moveStock(
                        $detail['product_id'],
                        $detail['karat_id'],
                        2,
                        1,
                        $typeMove,
                        abs($difference),
                        $weight,                    // <-- kirim berat di sini
                        'StockAdjustment',
                        $adjustment->id,
                        'Penyesuaian stock opname',
                        auth()->id(),
                        $detail['gold_type'] ?? 'new'
                    );
                }
            }

        });

        return redirect()->route('opname.index')->with('status', 'Stock opname berhasil disimpan.');
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

        // Gunakan toCollection (bukan toArray)
        $rows = Excel::toCollection(new \stdClass, $request->file('file'))->first();

        if ($rows->count() < 2) {
            return back()->with('error', 'File kosong atau format tidak valid.');
        }

        DB::beginTransaction();

        try {
            // 1️⃣ Buat Header Stock Adjustment
            $adjustment = StockAdjustment::create([
                'branch_id' => auth()->user()->branch_id ?? 2,
                'storage_location_id' => 1,
                'adjustment_date' => now(),
                'note' => 'Import Stock Opname',
                'created_by' => auth()->id(),
            ]);

            foreach ($rows as $index => $row) {

                // Skip header
                if ($index == 0) continue;

                if (!$row[0]) continue; // Skip baris kosong

                $productName = trim($row[0]);                 // kolom A
                $karatName   = trim($row[1]);                 // kolom B
                $goldType    = strtolower(trim($row[2] ?? 'new')); // kolom C
                $actualQty   = (float) ($row[3] ?? 0);         // kolom D
                $weight      = isset($row[4]) ? (float) $row[4] : null; // kolom E

                // 2️⃣ Ambil product & karat
                $product = Product::firstOrCreate(['name' => $productName]);
                $karat   = Karat::firstOrCreate(['name' => $karatName]);

                // 3️⃣ Ambil system qty dari STOCK
                $stock = Stock::where('product_id', $product->id)
                            ->where('karat_id', $karat->id)
                            ->where('weight', $weight)
                            ->where('type', $goldType)          // new / second
                            ->first();

                $systemQty = $stock?->quantity ?? 0;

                // 4️⃣ Hitung selisih
                $difference = $actualQty - $systemQty;

                // 5️⃣ Simpan detail opname
                StockAdjustmentDetail::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $product->id,
                    'karat_id' => $karat->id,
                    'system_qty' => $systemQty,
                    'actual_qty' => $actualQty,
                    'difference' => $difference,
                    'type' => $goldType,        // <-- simpan gold type, bukan 'in'/'out'
                    'weight' => $weight,
                ]);

                // 6️⃣ Catat stock movement jika ada perubahan
                if ($difference != 0) {
                    $movementType = $difference > 0 ? 'in' : 'out';

                    StockHelper::moveStock(
                        product_id: $product->id,
                        karat_id: $karat->id,
                        branchId: $adjustment->branch_id,
                        storageLocationId: $adjustment->storage_location_id,
                        type: $movementType,
                        quantity: $actualQty,
                        weight: $weight,
                        referenceType: 'StockAdjustment',
                        referenceId: $adjustment->id,
                        note: "Stock Opname Import",
                        userId: auth()->id(),
                        goldType: $goldType
                    );
                }
            }

            DB::commit();

            return redirect()->route('opname.index')
                ->with('status', 'saved');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id){
        $adjustment = StockAdjustment::with('details')->findOrFail($id);

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->details as $detail) {
                $difference = $detail->difference;
                $weight = $detail->weight;

                if ($difference != 0) {
                    $typeMove = $difference > 0 ? 'out' : 'in';
                    // rollback stok
                    \App\Helpers\StockHelper::moveStock(
                        $detail->product_id,
                        $detail->karat_id,
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

        return redirect()->route('opname.index')->with('status', 'Stock opname berhasil dihapus.');
    }


}
