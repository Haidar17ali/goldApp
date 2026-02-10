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
use Illuminate\Support\Str;

class GoldConversionController extends BaseController
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
        ])
            ->whereNull('gram')

            // hanya yang punya stock
            // ->whereHas('stocks', function ($q) {
            //     $q->where('weight', '>', 0);
            // })

            // ambil weight stock sebagai attribute
            // ->select('product_variants.*')
            // ->selectSub(function ($q) {
            //     $q->from('stocks')
            //         ->select('weight')
            //         ->whereColumn('stocks.product_variant_id', 'product_variants.id')
            //         ->where('weight', '>', 0)
            //         ->limit(1);
            // }, 'weight')

            ->get();
        // dd($productVariants);



        return view('pages.gold-conversion.create', [
            'productVariants'  => $productVariants,
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
            'stock_id'      => 'required|exists:product_variants,id',
            // 'input_weight'  => 'required|numeric|min:0.01',
            'note'          => 'nullable|string',

            'details'                   => 'required|array|min:1',
            'details.*.product_id'      => 'required|exists:products,id',
            'details.*.weight'          => 'required|numeric|min:0.01',
            'details.*.note'            => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {

                $productVariant = ProductVariant::with(["stocks"])->find($validated['stock_id']);

                $stock = Stock::firstOrCreate(
                    [
                        "product_variant_id" => $productVariant->id,
                    ],
                    [
                        'branch_id' => 1,
                        'storage_location_id' => 1,
                        'weight' => 0,
                        'type' => $productVariant->type,
                        'quantity' => 0,
                    ]
                );

                // =======================================================================================
                // 1. Simpan HEADER
                // =======================================================================================
                $conversion = GoldConversion::create([
                    'stock_id'      => $stock->id,
                    'product_variant_id'    => $productVariant->id,
                    'input_weight'  => array_sum(array_column($request->details, "weight")),
                    'note'          => $validated['note'] ?? null,
                    'created_by'    => auth()->id(),
                ]);


                // =======================================================================================
                // 2. Keluarkan stok gelondongan (OUT)
                // =======================================================================================
                StockHelper::moveStock(
                    $productVariant->id,
                    1,
                    1,
                    'out',
                    1,
                    array_sum(array_column($request->details, "weight")),
                    'GoldConversion',
                    $conversion->id,
                    'pecah-gelondongan',
                    auth()->id(),
                    $productVariant->type // biasanya "second"
                );

                // =======================================================================================
                // 3. Simpan DETAIL + tambahkan stok hasil pecahan
                // =======================================================================================
                foreach ($validated['details'] as $index => $d) {

                    $newPV = ProductVariant::firstOrCreate(
                        [
                            "product_id" => $d["product_id"],
                            "karat_id" => $productVariant->karat?->id ?? 0,
                            "gram" => $d["weight"],
                            'type'       => $productVariant->type == "new" ? $productVariant->type : "sepuh",
                        ],
                        [
                            'sku'           => strtoupper(
                                $d["product_id"] . '-' . $productVariant->karat->name . '-' .
                                    $d["weight"] . '-' .
                                    ($productVariant->type == "new" ? $productVariant->type : "sepuh")
                            ),
                            'barcode'       => strtoupper(Str::random(12)),
                            'default_price' => 0,
                        ]
                    );


                    GoldConversionOutput::create([
                        'gold_conversion_id' => $conversion->id,
                        'product_variant_id'         => $newPV->id,
                        'weight'             => $d['weight'],
                        'note'               => $d['note'] ?? null,
                    ]);

                    // stok masuk untuk item hasil pecahan
                    StockHelper::moveStock(
                        $newPV->id,
                        1,
                        1,
                        'in',
                        1,
                        $d['weight'],
                        'GoldConversionOutput',
                        $conversion->id,
                        'hasil-pecahan',
                        auth()->id(),
                        $productVariant->type == "new" ? $productVariant->type : "sepuh", // tetap second
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
        $conversion = GoldConversion::with(['stock', "productVariant", 'outputs.productVariant.product', 'outputs.productVariant.karat'])
            ->findOrFail($id);

        return view('pages.gold-conversion.show', compact('conversion'));
    }


    public function edit($id)
    {
        $conversion = GoldConversion::with(['outputs', 'stock.productVariant.product', 'stock.productVariant.karat'])
            ->findOrFail($id);

        $selectedVariantId = $conversion->product_variant_id;


        $currentStockId = $conversion->stock_id;

        $productVariants = ProductVariant::with([
            'product:id,name',
            'karat:id,name',
        ])
            ->where(function ($q) use ($selectedVariantId) {

                // =====================
                // CREATE MODE
                // =====================
                $q->where(function ($q2) {
                    $q2->whereNull('gram')
                        ->whereHas('stocks', function ($s) {
                            $s->where('weight', '>', 0);
                        });
                });

                // =====================
                // EDIT MODE (PASTI MUNCUL)
                // =====================
                if ($selectedVariantId) {
                    $q->orWhere('id', $selectedVariantId);
                }
            })

            ->select('product_variants.*')

            // ambil stock weight (boleh 0)
            ->selectSub(function ($q) {
                $q->from('stocks')
                    ->select('weight')
                    ->whereColumn('stocks.product_variant_id', 'product_variants.id')
                    ->orderByDesc('weight')
                    ->limit(1);
            }, 'weight')

            ->get();


        $products = Product::all();
        $karats = Karat::all();

        return view('pages.gold-conversion.edit', compact(
            'conversion',
            'productVariants',
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
        $productVariant = ProductVariant::find($validated['stock_id']);

        try {

            //---------------------------------------------------------
            // 1. KEMBALIKAN stok lama (rollback output lama)
            //---------------------------------------------------------

            foreach ($conversion->outputs as $old) {
                StockHelper::moveStock(
                    $old->product_variant_id,
                    $conversion->stock->branch_id,
                    $conversion->stock->storage_location_id,
                    'out',            // kebalikan dari proses input
                    1,
                    $old->weight,
                    'GoldConversion',
                    $conversion->id,
                    'rollback-output',
                    auth()->id(),
                    $old->productVariant->type
                );
            }

            // rollback bahan baku (stock_id)
            StockHelper::moveStock(
                $conversion->product_variant_id,
                $conversion->stock->branch_id,
                $conversion->stock->storage_location_id,
                'in',   // karena saat create dulu keluar (out)
                1,
                $conversion->input_weight,
                'GoldConversion',
                $conversion->id,
                'rollback-input',
                auth()->id(),
                $productVariant->type
            );

            //---------------------------------------------------------
            // 2. HAPUS output lama
            //---------------------------------------------------------
            $conversion->outputs()->delete();


            //---------------------------------------------------------
            // 3. UPDATE HEADER
            //---------------------------------------------------------

            $conversion->update([
                'stock_id'     => $productVariant->stocks->id,
                'product_variant_id'   => $productVariant->id,
                'karat_id'     => $productVariant->karat_id,
                'input_weight' => array_sum(array_column($request->details, "weight")),
                'note'         => $validated['note'] ?? null,
                'edited_by'    => auth()->id(),
            ]);

            //---------------------------------------------------------
            // 4. CATAT PERGERAKAN STOK BARU
            //---------------------------------------------------------
            // stok utama keluar lagi
            StockHelper::moveStock(
                $productVariant->id,
                1,
                1,
                'out',
                1,
                array_sum(array_column($request->details, "weight")),
                'GoldConversion',
                $conversion->id,
                'edit-input',
                auth()->id(),
                $productVariant->type
            );

            // simpan output + catat stock in
            foreach ($validated['details'] as $item) {
                $product = Product::where("id", $item["product_id"])->first();
                $newPV = ProductVariant::firstOrCreate(
                    [
                        "product_id" => $item["product_id"],
                        "karat_id" => $productVariant->karat?->id ?? 0,
                        "gram" => $item["weight"],
                        'type'       => $productVariant->type == "new" ? $productVariant->type : "sepuh",
                    ],
                    [
                        'sku'           => strtoupper(
                            $product->name . '-' .
                                $productVariant->karat->name . '-' .
                                $item["weight"] . '-' .
                                (
                                    $productVariant->type == "new"
                                    ? $productVariant->type
                                    : "sepuh"
                                )
                        ),
                        'barcode'       => strtoupper(Str::random(12)),
                        'default_price' => 0,
                    ]
                );

                $output = $conversion->outputs()->create([
                    'product_variant_id' => $newPV->id,
                    'karat_id'   => $request->karat_id, // ikut header
                    'weight'     => $item['weight'],
                    'note'       => null,
                ]);

                // stok output masuk
                StockHelper::moveStock(
                    $newPV->id,
                    1,
                    1,
                    'in',
                    1,
                    $item['weight'],
                    'GoldConversion',
                    $conversion->id,
                    'edit-output',
                    auth()->id(),
                    $newPV->type
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
        // 1. KEMBALIKAN stok lama (rollback output lama) output detail
        //---------------------------------------------------------

        foreach ($conversion->outputs as $old) {
            StockHelper::moveStock(
                $old->product_variant_id,
                1,
                1,
                'out',            // kebalikan dari proses input
                1,
                $old->weight,
                'GoldConversion',
                $conversion->id,
                'rollback-output',
                auth()->id(),
                $stock->type == "new" ? $stock->type : "sepuh"
            );
        }

        // rollback bahan baku (stock_id)
        StockHelper::moveStock(
            $conversion->product_variant_id,
            1,
            1,
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
