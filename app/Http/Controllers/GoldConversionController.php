<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\GoldStock;
use App\Models\Product;
use App\Models\Karat;
use App\Models\GoldConversion;
use App\Models\GoldConversionOutput;
use App\Models\ProductVariant;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoldConversionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX – daftar semua proses pecah emas
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('pages.gold-conversion.index');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE – halaman form pecah stok emas
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
            'stocks' => function ($q) {
                $q->where('quantity', '>', 0);
            }
        ])->where("gram", null)->get();

        return view('pages.gold-conversion.create', [
            'stocks'  => Stock::where("product_id", 7)->whereIn('type', ['second', 'new'])->where("weight", ">", 0)->get(),
            'products' => Product::all(),
            'karats' => Karat::all(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE – simpan proses pecah stok emas
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'stock_id'      => 'required|exists:stocks,id',
            'input_weight'  => 'required|numeric|min:0.01',
            'note'          => 'nullable|string',

            'details'                   => 'required|array|min:1',
            'details.*.product_id'      => 'required|exists:products,id',
            'details.*.weight'          => 'required|numeric|min:0.01',
            'details.*.note'            => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {

                $stock = Stock::find($validated['stock_id']);

                // =======================================================================================
                // 1. Simpan HEADER
                // =======================================================================================
                $conversion = GoldConversion::create([
                    'stock_id'      => $stock->id,
                    'product_id'    => $stock->product_id,
                    'karat_id'      => $stock->karat_id,
                    'input_weight'  => array_sum(array_column($request->details, "weight")),
                    'note'          => $validated['note'] ?? null,
                    'created_by'    => auth()->id(),
                ]);

                // =======================================================================================
                // 2. Keluarkan stok gelondongan (OUT)
                // =======================================================================================
                StockHelper::moveStock(
                    $stock->product_id,
                    $stock->karat_id,
                    2,
                    1,
                    'out',
                    1,
                    array_sum(array_column($request->details, "weight")),
                    'GoldConversion',
                    $conversion->id,
                    'pecah-gelondongan',
                    auth()->id(),
                    $stock->type // biasanya "second"
                );

                // =======================================================================================
                // 3. Simpan DETAIL + tambahkan stok hasil pecahan
                // =======================================================================================
                foreach ($validated['details'] as $d) {

                    GoldConversionOutput::create([
                        'gold_conversion_id' => $conversion->id,
                        'product_id'         => $d['product_id'],
                        'karat_id'           => $request->karat_id,
                        'weight'             => $d['weight'],
                        'note'               => $d['note'] ?? null,
                    ]);

                    // stok masuk untuk item hasil pecahan
                    StockHelper::moveStock(
                        $d['product_id'],
                        $request->karat_id,
                        $stock->branch_id,
                        $stock->storage_location_id,
                        'in',
                        1,
                        $d['weight'],
                        'GoldConversionOutput',
                        $conversion->id,
                        'hasil-pecahan',
                        auth()->id(),
                        $stock->type // tetap second
                    );
                }
            });

            return redirect()->route("konversi-emas.index")->with("status", "saved");
        } catch (\Throwable $e) {

            \Log::error("Gagal menyimpan gold conversion", [
                "msg" => $e->getMessage(),
                "trace" => $e->getTraceAsString(),
            ]);

            return response()->json([
                "success" => false,
                "message" => "Gagal menyimpan: " . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $conversion = GoldConversion::with(['stock', 'kadar', 'outputs.product', 'outputs.kadar'])
            ->findOrFail($id);

        return view('pages.gold-conversion.show', compact('conversion'));
    }


    public function edit($id)
    {
        $conversion = GoldConversion::with(['outputs', 'stock.product', 'stock.karat'])
            ->findOrFail($id);

        $currentStockId = $conversion->stock_id;
        $stocks = Stock::where("product_id", 7)
            ->whereIn('type', ['second', 'new'])
            ->where(function ($q) use ($currentStockId) {
                $q->where("weight", ">", 0)
                    ->orWhere("id", $currentStockId); // tampilkan walaupun weight = 0
            })
            ->get();

        $products = Product::all();
        $karats = Karat::all();

        return view('pages.gold-conversion.edit', compact(
            'conversion',
            'stocks',
            'products',
            'karats'
        ));
    }

    public function update(Request $request, $id)
    {
        $conversion = GoldConversion::with('outputs')->findOrFail($id);

        $validated = $request->validate([
            'stock_id'      => 'required|exists:stocks,id',
            'karat_id'      => 'required|exists:karats,id',
            'input_weight'  => 'required|numeric|min:0.001',
            'note'          => 'nullable|string',
            'details'       => 'required|array|min:1',
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.weight'     => 'required|numeric|min:0.001',
        ]);

        DB::beginTransaction();
        $stock = Stock::find($validated['stock_id']);

        try {

            //---------------------------------------------------------
            // 1. KEMBALIKAN stok lama (rollback output lama)
            //---------------------------------------------------------

            foreach ($conversion->outputs as $old) {
                StockHelper::moveStock(
                    $old->product_id,
                    $old->karat_id,
                    $conversion->stock->branch_id,
                    $conversion->stock->storage_location_id,
                    'out',            // kebalikan dari proses input
                    1,
                    $old->weight,
                    'GoldConversion',
                    $conversion->id,
                    'rollback-output',
                    auth()->id(),
                    $stock->type
                );
            }

            // rollback bahan baku (stock_id)
            StockHelper::moveStock(
                $conversion->product_id,
                $conversion->karat_id,
                $conversion->stock->branch_id,
                $conversion->stock->storage_location_id,
                'in',   // karena saat create dulu keluar (out)
                1,
                $conversion->input_weight,
                'GoldConversion',
                $conversion->id,
                'rollback-input',
                auth()->id(),
                $stock->type
            );

            //---------------------------------------------------------
            // 2. HAPUS output lama
            //---------------------------------------------------------
            $conversion->outputs()->delete();


            //---------------------------------------------------------
            // 3. UPDATE HEADER
            //---------------------------------------------------------

            $conversion->update([
                'stock_id'     => $validated['stock_id'],
                'product_id'   => Stock::find($validated['stock_id'])->product_id,
                'karat_id'     => $validated['karat_id'],
                'input_weight' => array_sum(array_column($request->details, "weight")),
                'note'         => $validated['note'] ?? null,
                'edited_by'    => auth()->id(),
            ]);


            //---------------------------------------------------------
            // 4. CATAT PERGERAKAN STOK BARU
            //---------------------------------------------------------
            // stok utama keluar lagi
            StockHelper::moveStock(
                $conversion->product_id,
                $conversion->karat_id,
                $conversion->stock->branch_id,
                $conversion->stock->storage_location_id,
                'out',
                1,
                array_sum(array_column($request->details, "weight")),
                'GoldConversion',
                $conversion->id,
                'edit-input',
                auth()->id(),
                $stock->type
            );

            // simpan output + catat stock in
            foreach ($validated['details'] as $item) {

                $output = $conversion->outputs()->create([
                    'product_id' => $item['product_id'],
                    'karat_id'   => $request->karat_id, // ikut header
                    'weight'     => $item['weight'],
                    'note'       => null,
                ]);

                // stok output masuk
                StockHelper::moveStock(
                    $item['product_id'],
                    $validated['karat_id'],
                    $conversion->stock->branch_id,
                    $conversion->stock->storage_location_id,
                    'in',
                    1,
                    $item['weight'],
                    'GoldConversion',
                    $conversion->id,
                    'edit-output',
                    auth()->id(),
                    $stock->type
                );
            }

            DB::commit();

            return redirect()->route("konversi-emas.index")->with('status', 'edited');
        } catch (\Throwable $e) {

            DB::rollBack();
            \Log::error("Gagal update Gold Conversion: " . $e->getMessage());

            return back()->withErrors([
                'msg' => 'Gagal update: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        $conversion = GoldConversion::with('outputs')->findOrFail($id);

        DB::beginTransaction();
        $stock = Stock::find($conversion->stock_id);

        //---------------------------------------------------------
        // 1. KEMBALIKAN stok lama (rollback output lama)
        //---------------------------------------------------------

        foreach ($conversion->outputs as $old) {
            StockHelper::moveStock(
                $old->product_id,
                $old->karat_id,
                $conversion->stock->branch_id,
                $conversion->stock->storage_location_id,
                'out',            // kebalikan dari proses input
                1,
                $old->weight,
                'GoldConversion',
                $conversion->id,
                'rollback-output',
                auth()->id(),
                $stock->type
            );
        }

        // rollback bahan baku (stock_id)
        StockHelper::moveStock(
            $conversion->product_id,
            $conversion->karat_id,
            $conversion->stock->branch_id,
            $conversion->stock->storage_location_id,
            'in',   // karena saat create dulu keluar (out)
            1,
            $conversion->input_weight,
            'GoldConversion',
            $conversion->id,
            'rollback-input',
            auth()->id(),
            $stock->type
        );

        // ========================================================
        // 3. Hapus output (detail)
        // ========================================================
        $conversion->outputs()->delete();

        // ========================================================
        // 4. Hapus master conversion
        // ========================================================
        $conversion->delete();

        DB::commit();

        return redirect()->route('konversi-emas.index')
            ->with(['status' => 'deleted']);
    }
}
