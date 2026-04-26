<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\GoldPrice;
use App\Models\GoldPriceDetail;


class GoldPriceController extends Controller
{
    public function index()
    {
        $goldPrices = \App\Models\GoldPrice::with('details')
            ->orderByDesc('active_at')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->active_at)->format('Y-m-d');
            });

        return view('pages.gold-prices.index', compact('goldPrices'));
    }

    public function create()
    {
        $karats = \App\Models\Karat::orderBy('name')->get();

        return view('pages.gold-prices.create', compact('karats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'active_at' => 'required|date',
            'price_24k' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $activeAt = Carbon::parse($request->active_at);

            // expire yang lama
            $currentActive = GoldPrice::whereNull('expired_at')->get();

            foreach ($currentActive as $old) {
                $old->update([
                    'expired_at' => $activeAt->copy()->subDay()
                ]);
            }

            // insert header
            $goldPrice = GoldPrice::create([
                'active_at' => $activeAt,
                'expired_at' => null
            ]);

            // 🔥 ambil karat 24K
            $karat24 = \App\Models\Karat::where('name', '24K')->first();

            GoldPriceDetail::create([
                'gold_price_id' => $goldPrice->id,
                'karat_id' => $karat24->id,
                'price' => $request->price_24k,
            ]);
        });

        return redirect()
            ->route('set-harga.index')
            ->with('status', 'saved');
    }
}
