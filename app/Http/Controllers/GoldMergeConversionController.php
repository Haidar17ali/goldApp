<?php

namespace App\Http\Controllers;

use App\Helpers\StockHelper;
use App\Models\GoldMergeConversion;
use App\Models\GoldMergeConversionInput;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Karat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoldMergeConversionController extends Controller
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
            'products' => Product::where('name', "!=", 'emas')->get(),
            'karats'   => Karat::orderBy('name')->get(),
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
            'details.*.product_id' => 'required|exists:products,id',
            'details.*.karat_id'   => 'required|exists:karats,id',
            'details.*.weight'     => 'required|numeric|min:0.001',
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
            foreach ($validated['details'] as $row) {

                $conversion->inputs()->create($row);
                
                StockHelper::moveStock(
                    $row['product_id'],
                    $row['karat_id'],
                    2, // etalase
                    1,
                    'out',
                    $row["qty"],
                    $row['weight'],
                    'GoldMergeConversion',
                    $conversion->id,
                    'keluar-etalase',
                    auth()->id(),
                    'new'
                );

            }

            // =============================================
            // 2. GROUP PER KARAT → MASUK EMAS
            // =============================================
            $grouped = collect($validated['details'])
                ->groupBy('karat_id');

            $emas = Product::firstOrCreate(
                ['name' => 'emas'],
                ['code' => 'ems']
            );


            foreach ($grouped as $karatId => $items) {

                $totalWeight = $items->sum('weight');

                StockHelper::moveStock(
                    $emas->id, // emas
                    $karatId,
                    2, // brankas
                    1,
                    'in',
                    $row["qty"],
                    $row['weight'],
                    'GoldMergeConversion',
                    $conversion->id,
                    'Masuk brankas',
                    auth()->id(),
                    'new'
                );

            }
        });

        return redirect()->route('keluar-etalase.index')->with('status', 'saved');
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $conversion = GoldMergeConversion::with('inputs.stock.product', 'inputs.stock.karat')
            ->findOrFail($id);

        $stocks = Stock::where('product_id', '!=', 7)
            ->where(function ($q) use ($conversion) {
                $q->where('weight', '>', 0)
                  ->orWhereIn('id', $conversion->inputs->pluck('stock_id'));
            })->get();

        return view('pages.gold-merge.edit', [
            'conversion' => $conversion,
            'stocks' => $stocks,
            'karats' => Karat::all(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $id)
    {
        $conversion = GoldMergeConversion::with('inputs.stock')->findOrFail($id);

        $validated = $request->validate([
            'karat_id'        => 'required|exists:karats,id',
            'output_weight'   => 'required|numeric|min:0.001',
            'note'            => 'nullable|string',

            'details'                   => 'required|array|min:1',
            'details.*.stock_id'        => 'required|exists:stocks,id',
            'details.*.weight'          => 'required|numeric|min:0.001',
        ]);

        DB::beginTransaction();

        try {

            // =========================================================
            // 1. ROLLBACK OUTPUT EMAS
            // =========================================================
            StockHelper::moveStock(
                7,
                $conversion->karat_id,
                2,
                1,
                'out',
                1,
                $conversion->output_weight,
                'GoldMergeConversion',
                $conversion->id,
                'rollback-emas',
                auth()->id(),
                'second'
            );

            // =========================================================
            // 2. ROLLBACK INPUT ETALASE
            // =========================================================
            foreach ($conversion->inputs as $old) {

                $stock = $old->stock;

                StockHelper::moveStock(
                    $stock->product_id,
                    $stock->karat_id,
                    $stock->branch_id,
                    $stock->storage_location_id,
                    'in',
                    1,
                    $old->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'rollback-etalase',
                    auth()->id(),
                    $stock->type
                );
            }

            $conversion->inputs()->delete();

            // =========================================================
            // 3. UPDATE HEADER
            // =========================================================
            $conversion->update([
                'karat_id'      => $validated['karat_id'],
                'output_weight' => $validated['output_weight'],
                'note'          => $validated['note'] ?? null,
                'edited_by'     => auth()->id(),
            ]);

            // =========================================================
            // 4. INPUT BARU
            // =========================================================
            foreach ($validated['details'] as $item) {

                $stock = Stock::find($item['stock_id']);

                $conversion->inputs()->create([
                    'stock_id' => $stock->id,
                    'weight'   => $item['weight'],
                ]);

                StockHelper::moveStock(
                    $stock->product_id,
                    $stock->karat_id,
                    $stock->branch_id,
                    $stock->storage_location_id,
                    'out',
                    1,
                    $item['weight'],
                    'GoldMergeConversion',
                    $conversion->id,
                    'edit-etalase',
                    auth()->id(),
                    $stock->type
                );
            }

            StockHelper::moveStock(
                7,
                $validated['karat_id'],
                2,
                1,
                'in',
                1,
                $validated['output_weight'],
                'GoldMergeConversion',
                $conversion->id,
                'edit-emas',
                auth()->id(),
                'second'
            );

            DB::commit();

            return redirect()->route('gold-merge.index')->with('status', 'edited');

        } catch (\Throwable $e) {

            DB::rollBack();
            throw $e;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */
    public function destroy($id)
    {
        $conversion = GoldMergeConversion::with('inputs.stock')->findOrFail($id);

        DB::transaction(function () use ($conversion) {

            // rollback emas
            StockHelper::moveStock(
                7,
                $conversion->karat_id,
                2,
                1,
                'out',
                1,
                $conversion->output_weight,
                'GoldMergeConversion',
                $conversion->id,
                'delete-emas',
                auth()->id(),
                'second'
            );

            // rollback etalase
            foreach ($conversion->inputs as $input) {

                $stock = $input->stock;

                StockHelper::moveStock(
                    $stock->product_id,
                    $stock->karat_id,
                    $stock->branch_id,
                    $stock->storage_location_id,
                    'in',
                    1,
                    $input->weight,
                    'GoldMergeConversion',
                    $conversion->id,
                    'delete-etalase',
                    auth()->id(),
                    $stock->type
                );
            }

            $conversion->inputs()->delete();
            $conversion->delete();
        });

        return redirect()->route('gold-merge.index')->with('status', 'deleted');
    }

}
