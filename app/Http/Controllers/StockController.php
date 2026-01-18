<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function index(Request $request)
    {
        $query = Stock::query()
            ->join('product_variants as pv', 'pv.id', '=', 'stocks.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->leftJoin('karats as k', 'k.id', '=', 'pv.karat_id')
            ->where('stocks.branch_id', auth()->user()->branch_id ?? 1);

        /**
         * 🔍 SEARCH
         * - nama produk
         */
        if ($request->filled('search')) {
            $query->where('p.name', 'like', '%' . $request->search . '%');
        }

        /**
         * 🔍 FILTER VARIANT TYPE
         * new | sepuh
         */
        if ($request->filled('variant_type')) {
            $query->where('pv.type', $request->variant_type);
        }

        /**
         * 📊 GELONDONGAN AGGREGATION
         */
        $stocks = $query
            ->selectRaw('
                p.id as product_id,
                p.name as product_name,
                k.name as karat_name,
                pv.type as variant_type,
                SUM(stocks.quantity) as total_qty,
                SUM(stocks.quantity * pv.gram) as total_weight
            ')
            ->groupBy(
                'p.id',
                'p.name',
                'k.name',
                'pv.type'
            )
            ->orderBy('p.name')
            ->paginate(15)
            ->withQueryString();

        return view('pages.stocks.index', compact('stocks'));
    }


    public function detail(Request $request)
    {
        $request->validate([
            'product' => 'required|exists:products,id',
            'type' => 'required|in:new,sepuh',
        ]);

        $branchId = auth()->user()->branch_id ?? 1;

        $query = Stock::query()
            ->join('product_variants as pv', 'pv.id', '=', 'stocks.product_variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->leftJoin('karats as k', 'k.id', '=', 'pv.karat_id')
            ->join('storage_locations as sl', 'sl.id', '=', 'stocks.storage_location_id')
            ->where('stocks.branch_id', $branchId)
            ->where('pv.product_id', $request->product)
            ->where('pv.type', $request->type);

        // filter karat (opsional)
        if ($request->filled('karat')) {
            $query->where('k.name', $request->karat);
        }

        $stocks = $query
            ->select([
                'pv.id as variant_id',
                'pv.sku',
                'pv.gram',
                'pv.type as variant_type',
                'p.name as product_name',
                'k.name as karat_name',
                'sl.name as location_name',
                'stocks.type as stock_type',
                'stocks.quantity',
            ])
            ->orderBy('pv.gram')
            ->paginate(20)
            ->withQueryString();

        return view('pages.stocks.detail', compact('stocks'));
    }




    public function info(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'karat_id'   => 'required|integer',
            'weight'     => 'required|numeric|min:0.001',
        ]);

        $query = Stock::where('product_id', $request->product_id)
            ->where('karat_id', $request->karat_id)
            ->where('weight', $request->weight)->first();

        return response()->json([
            'qty'       => $query->quantity,          // JUMLAH ITEM
            'available' => $query->exists(),         // ADA / TIDAK
        ]);
    }

    public function weights(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $weights = Stock::where('product_variant_id', $request->product_id)
            ->pluck('quantity')
            ->unique()
            ->sort()
            ->map(fn($q) => number_format($q, 0, ',', '.'))
            ->values();


        return response()->json($weights);
    }
}
