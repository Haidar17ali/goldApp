<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\GoldMergeConversion;
use App\Models\GoldMergeConversionInput;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Karat;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GoldMergeConversionController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        return view('pages.gold-merge.index');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('pages.gold-merge.create', [
            'productVariants' => ProductVariant::where('gram', "!=", null)->whereHas('stocks', function ($q) {
                $q->where('quantity', '>', 0);
            })->get(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | STORE (Anting → Emas)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'note' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_variant_id' => 'required|exists:product_variants,id',
            'details.*.qty'     => 'required|numeric',
        ]);

        DB::transaction(function () use ($validated) {

            $conversion = GoldMergeConversion::create([
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $start = microtime(true);

            // =============================================
            // 1. CATAT DETAIL & KELUARKAN STOK ETALASE
            // =============================================
            foreach ($validated['details'] as $index => $row) {
                $pv = ProductVariant::findOrFail($row["product_variant_id"]);

                $conversion->inputs()->create($row);

                StockHelper::moveStock(
                    $pv->id,
                    1, // etalase
                    1,
                    'out',
                    $row["qty"],
                    $pv->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'keluar-etalase',
                    auth()->id(),
                    $pv->type
                );
            }

            // =============================================
            // 2. GROUP PER KARAT → MASUK EMAS
            // =============================================
            $grouped = collect($validated['details'])->map(function ($row) {
                $pv = ProductVariant::find($row['product_variant_id']);

                return [
                    'karat_id' => $pv->karat_id,
                    'type'     => $pv->type,
                    'qty'      => $row['qty'],
                    'weight'   => $pv->weight * $row['qty'],
                ];
            })->groupBy('karat_id');


            $emas = Product::firstOrCreate(
                ['name' => 'emas'],
                ['code' => 'ems']
            );

            $pvGold = ProductVariant::firstOrCreate(
                [
                    "product_id" => $emas->id,
                    "karat_id" => $pv->karat_id,
                    "gram" => null,
                    "type" => $pv->type
                ],
                [
                    'sku' => strtoupper($emas->name . '-' . ($pv->karat?->name ?? 'NOKRT') . "-" . $pv->type),
                    'barcode' => strtoupper(Str::random(6)),
                    'default_price' => 0,
                ]
            );


            foreach ($grouped as $karatId => $items) {

                $totalWeight = $items->sum('weight');

                StockHelper::moveStock(
                    $pvGold->id, // emas
                    1, // brankas
                    1,
                    'in',
                    $row["qty"],
                    $pv->gram * $row["qty"],
                    'GoldMergeConversion',
                    $conversion->id,
                    'Masuk brankas',
                    auth()->id(),
                    $pv->type
                );
            }
        });

        return redirect()->route('keluar-etalase.index')->with('status', 'saved');
    }

    public function show($id)
    {
        $conversion = GoldMergeConversion::with('inputs.productVariant.product', 'inputs.productVariant.karat')->findOrFail($id);
        return view('pages.gold-merge.show', compact(
            'conversion',
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $conversion = GoldMergeConversion::with('inputs.productVariant.product', 'inputs.productVariant.karat')->findOrFail($id);

        $productVariants = ProductVariant::with('product', 'karat')->get();

        return view('pages.gold-merge.edit', compact(
            'conversion',
            'productVariants'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $conversion = GoldMergeConversion::with('inputs.productVariant.product', 'inputs.productVariant.karat')->findOrFail($id);
        $validated = $request->validate([
            'note' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.product_variant_id' => 'required|exists:product_variants,id',
            'details.*.qty' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated, $conversion) {

            // ==================================================
            // 1. ROLLBACK STOK LAMA (KELUAR ETALASE) masukan etalase lagi
            // ==================================================
            foreach ($conversion->inputs as $input) {

                $pv = $input->productVariant;

                StockHelper::moveStock(
                    $pv->id,
                    1, // etalase
                    1,
                    'in',
                    $input->qty,
                    $pv->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'rollback edit',
                    auth()->id(),
                    $pv->type
                );
            }

            // ==================================================
            // 2. ROLLBACK EMAS (BRANKAS)
            // HITUNG ULANG BERDASARKAN INPUT LAMA rollback stock brankas
            // ==================================================
            $rollbackGrouped = $conversion->inputs->map(function ($input) {
                $pv = $input->productVariant;

                return [
                    'karat_id' => $pv->karat_id,
                    'weight' => $pv->gram * $input->qty,
                    'type' => $pv->type,
                ];
            })->groupBy('karat_id');

            $emas = Product::where('name', 'emas')->first();

            if ($emas) {
                foreach ($rollbackGrouped as $karatId => $items) {

                    $totalWeight = $items->sum('weight');

                    $pvGold = ProductVariant::where([
                        'product_id' => $emas->id,
                        'karat_id' => $karatId,
                        'gram' => null,
                        'type' => $pv->type,
                    ])->first();

                    if ($pvGold) {
                        StockHelper::moveStock(
                            $pvGold->id,
                            1, // brankas
                            1,
                            'out',
                            1,
                            $totalWeight,
                            'GoldMergeConversion',
                            $conversion->id,
                            'rollback edit emas',
                            auth()->id(),
                            'new'
                        );
                    }
                }
            }

            // ==================================================
            // 3. HAPUS INPUT LAMA
            // ==================================================
            $conversion->inputs()->delete();

            $conversion->update([
                'note' => $validated['note'] ?? null,
                'edited_by' => auth()->id(),
            ]);

            // ==================================================
            // 4. PROSES ULANG INPUT BARU
            // ==================================================
            foreach ($validated['details'] as $row) {

                $pv = ProductVariant::findOrFail($row['product_variant_id']);

                $conversion->inputs()->create([
                    'gold_merge_conversion_id' => $conversion->id,
                    'product_variant_id' => $pv->id,
                    'qty' => $row['qty'],
                ]);

                StockHelper::moveStock(
                    $pv->id,
                    1, // etalase
                    1,
                    'out',
                    $row['qty'],
                    $pv->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'keluar-etalase',
                    auth()->id(),
                    $pv->type
                );
            }

            // ==================================================
            // 5. GROUP INPUT BARU → MASUK EMAS
            // ==================================================
            $emas = Product::firstOrCreate(
                ['name' => 'emas'],
                ['code' => 'ems']
            );

            $grouped = collect($validated['details'])->map(function ($row) {
                $pv = ProductVariant::find($row['product_variant_id']);

                return [
                    'karat_id' => $pv->karat_id,
                    'weight' => $pv->gram * $row['qty'],
                ];
            })->groupBy('karat_id');

            foreach ($grouped as $karatId => $items) {

                $totalWeight = $items->sum('weight');

                $pvGold = ProductVariant::firstOrCreate(
                    [
                        'product_id' => $emas->id,
                        'karat_id' => $karatId,
                        'gram' => null,
                        'type' => $pv->type,
                    ],
                    [
                        'sku' => 'EMS-' . $karatId . $pv->type,
                        'barcode' => strtoupper(Str::random(6)),
                        'default_price' => 0,
                    ]
                );

                StockHelper::moveStock(
                    $pvGold->id,
                    1, // brankas
                    1,
                    'in',
                    1,
                    $totalWeight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'masuk brankas',
                    auth()->id(),
                    $pvGold->type
                );
            }
        });

        return redirect()
            ->route('keluar-etalase.index')
            ->with('status', 'updated');
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $conversion = GoldMergeConversion::with('inputs.productVariant')->findOrFail($id);

        DB::transaction(function () use ($conversion) {

            // =============================================
            // 1. GROUP INPUT → HITUNG TOTAL EMAS
            // =============================================
            $grouped = $conversion->inputs->map(function ($input) {
                $pv = $input->productVariant;

                return [
                    'karat_id' => $pv->karat_id,
                    'type'     => $pv->type,
                    'weight'   => $pv->gram * $input->qty,
                ];
            })->groupBy(fn($i) => $i['karat_id'] . '-' . $i['type']);

            $emas = Product::where('name', 'emas')->first();

            if ($emas) {
                foreach ($grouped as $group) {

                    $karatId = $group->first()['karat_id'];
                    $type    = $group->first()['type'];
                    $totalWeight = $group->sum('weight');

                    $pvGold = ProductVariant::where([
                        'product_id' => $emas->id,
                        'karat_id'   => $karatId,
                        'gram'       => null,
                        'type'       => $type,
                    ])->first();

                    if ($pvGold && $totalWeight > 0) {
                        StockHelper::moveStock(
                            $pvGold->id,
                            1, // brankas
                            1,
                            'out',
                            1,
                            $totalWeight,
                            'GoldMergeConversion',
                            $conversion->id,
                            'destroy-rollback-emas',
                            auth()->id(),
                            $type
                        );
                    }
                }
            }

            // =============================================
            // 2. ROLLBACK ETALASE
            // =============================================
            foreach ($conversion->inputs as $input) {
                $pv = $input->productVariant;

                StockHelper::moveStock(
                    $pv->id,
                    1, // etalase
                    1,
                    'in',
                    $input->qty,
                    $pv->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'destroy-rollback-etalase',
                    auth()->id(),
                    $pv->type
                );
            }

            // =============================================
            // 3. HAPUS DATA
            // =============================================
            $conversion->inputs()->delete();
            $conversion->delete();
        });

        return redirect()
            ->route('keluar-etalase.index')
            ->with('status', 'deleted');
    }
}
