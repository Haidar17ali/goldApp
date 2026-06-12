<?php

namespace App\Http\Controllers;

use App\Helpers\AccountingHelper;
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

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'active_at' => 'required|date',
    //         'price_24k' => 'required|numeric|min:0',
    //     ]);

    //     DB::transaction(function () use ($request) {

    //         $activeAt = Carbon::parse($request->active_at);

    //         // expire yang lama
    //         $currentActive = GoldPrice::whereNull('expired_at')->get();

    //         foreach ($currentActive as $old) {
    //             $old->update([
    //                 'expired_at' => $activeAt->copy()->subDay()
    //             ]);
    //         }

    //         // insert header
    //         $goldPrice = GoldPrice::create([
    //             'active_at' => $activeAt,
    //             'expired_at' => null
    //         ]);

    //         // 🔥 ambil karat 24K
    //         $karat24 = \App\Models\Karat::where('name', '24K')->first();

    //         GoldPriceDetail::create([
    //             'gold_price_id' => $goldPrice->id,
    //             'karat_id' => $karat24->id,
    //             'price' => $request->price_24k,
    //         ]);
    //     });

    //     return redirect()
    //         ->route('set-harga.index')
    //         ->with('status', 'saved');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'active_at' => 'required|date',
            'price_24k' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {

            $activeAt = Carbon::parse($request->active_at);

            // =========================
            // AMBIL KARAT 24K
            // =========================
            $karat24 = \App\Models\Karat::where('name', '24K')->firstOrFail();

            // =========================
            // HARGA LAMA 24K
            // =========================
            $oldPrice24K = GoldPriceDetail::where('karat_id', $karat24->id)
                ->latest('id')
                ->value('price') ?? 0;

            $newPrice24K = (float) $request->price_24k;

            // =========================
            // EXPIRE HARGA LAMA
            // =========================
            $currentActive = GoldPrice::whereNull('expired_at')->get();

            foreach ($currentActive as $old) {
                $old->update([
                    'expired_at' => $activeAt->copy()->subDay()
                ]);
            }

            // =========================
            // SIMPAN HEADER HARGA BARU
            // =========================
            $goldPrice = GoldPrice::create([
                'active_at' => $activeAt,
                'expired_at' => null
            ]);

            GoldPriceDetail::create([
                'gold_price_id' => $goldPrice->id,
                'karat_id'      => $karat24->id,
                'price'         => $newPrice24K,
            ]);

            // pertama kali input harga
            if ($oldPrice24K <= 0) {
                return;
            }

            // =========================
            // AKUN PER CABANG
            // =========================
            $persediaanAccounts = [
                1 => '103.01.001', // Pasuruan
                2 => '103.01.002', // Paserpan
                3 => '103.01.003', // Sandang Ayu
            ];

            $persediaanAccount = $persediaanAccounts[auth()->user()->profile->branch_id ?? 1] ?? '103.00.00';

            $revaluationAccounts = [
                1 => '501.02.001', // Pasuruan
                2 => '501.02.002', // Paserpan
                3 => '501.02.003', // Sandang Ayu
            ];

            // =========================
            // LOOP CABANG
            // =========================
            foreach (\App\Models\Branch::all() as $index => $branch) {

                $revaluationAmount = 0;

                $stocks = \App\Models\Stock::with([
                    'productVariant.karat'
                ])
                    ->where('branch_id', $branch->id)
                    ->where('quantity', '>', 0)
                    ->where('weight', null)
                    ->get();

                foreach ($stocks as $stock) {

                    if (
                        !$stock->productVariant ||
                        !$stock->productVariant->karat
                    ) {
                        continue;
                    }

                    $karat = $stock->productVariant->karat;

                    $percentage = (float) $karat->percentage;


                    if ($percentage <= 0) {
                        continue;
                    }

                    // =========================
                    // TOTAL GRAM
                    // =========================
                    $gram = 0;

                    if (
                        !is_null($stock->productVariant->gram)
                        && $stock->productVariant->gram > 0
                    ) {

                        $gram = $stock->productVariant->gram
                            * $stock->quantity;
                    } else {

                        $gram = (float) $stock->weight;
                    }

                    if ($gram <= 0) {
                        continue;
                    }

                    // =========================
                    // HARGA KARAT LAMA
                    // =========================
                    $oldKaratPrice =
                        $oldPrice24K * ($percentage / 100);

                    // =========================
                    // HARGA KARAT BARU
                    // =========================
                    $newKaratPrice =
                        $newPrice24K * ($percentage / 100);


                    // =========================
                    // SELISIH NILAI STOK
                    // =========================
                    $revaluationAmount +=
                        ($newKaratPrice - $oldKaratPrice)
                        * $gram;
                }

                if (round($revaluationAmount, 2) == 0) {
                    continue;
                }

                $lines = [];

                // =========================
                // HARGA NAIK
                // =========================
                if ($revaluationAmount > 0) {

                    $lines[] = [
                        'account' => $persediaanAccount,
                        'debit'   => abs($revaluationAmount),
                    ];

                    $lines[] = [
                        'account' => $revaluationAccounts[$branch->id],
                        'credit'  => abs($revaluationAmount),
                    ];
                }

                // =========================
                // HARGA TURUN
                // =========================
                else {

                    $lines[] = [
                        'account' => $revaluationAccounts[$branch->id],
                        'debit'   => abs($revaluationAmount),
                    ];

                    $lines[] = [
                        'account' => $persediaanAccount,
                        'credit'  => abs($revaluationAmount),
                    ];
                }

                if ($index == 1) {
                    # code...

                    dd($stocks);
                    dd($lines);
                }

                AccountingHelper::post([
                    'date' => $activeAt,
                    'reference' => 'REVALUE-' . $goldPrice->id . '-' . $branch->id,
                    'description' => 'Revaluasi harga emas cabang ' . $branch->name,
                    'source_type' => GoldPrice::class,
                    'source_id' => $goldPrice->id,
                    'lines' => $lines,
                ]);
            }
        });

        return redirect()
            ->route('set-harga.index')
            ->with('status', 'saved');
    }
}
