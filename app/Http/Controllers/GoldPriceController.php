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
            'details' => 'required|array',
            'details.*.karat_id' => 'required|exists:karats,id',
            'details.*.price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $activeAt = Carbon::parse($request->active_at);

            // 🔥 1. Cari harga yang masih aktif
            $currentActive = GoldPrice::whereNull('expired_at')->get();

            foreach ($currentActive as $old) {
                // 👉 set expired = H-1 dari active baru
                $old->update([
                    'expired_at' => $activeAt->copy()->subDay()
                ]);
            }

            // 🔥 2. Insert header baru
            $goldPrice = GoldPrice::create([
                'active_at' => $activeAt,
                'expired_at' => null
            ]);

            // 🔥 3. Insert detail per kadar
            foreach ($request->details as $detail) {
                GoldPriceDetail::create([
                    'gold_price_id' => $goldPrice->id,
                    'karat_id' => $detail['karat_id'],
                    'price' => $detail['price'],
                ]);
            }
        });

        return redirect()
            ->route('set-harga.index')
            ->with('status', 'saved');
    }
}
