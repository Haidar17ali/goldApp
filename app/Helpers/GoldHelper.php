<?php

namespace App\Helpers;

use App\Models\GoldPrice;

namespace App\Helpers;

use App\Models\GoldPrice;
use App\Models\Karat;

class GoldHelper
{
    public static function getHargaByKarat($karatId)
    {
        // 🔥 ambil harga emas aktif
        $goldPrice = GoldPrice::where(function ($q) {
            $q->whereNull('expired_at')
                ->orWhere('expired_at', '>=', now());
        })
            ->where('active_at', '<=', now())
            ->orderByDesc('active_at')
            ->first();

        $kadar24k = Karat::where("name", "24k")->pluck('id');

        if (!$goldPrice) {
            throw new \Exception('Harga emas aktif belum tersedia');
        }

        // =============================
        // 🔥 1. PRIORITAS: DETAIL
        // =============================
        $detail = $goldPrice->details()
            ->where('karat_id', $kadar24k)
            ->first();


        // =============================
        // 🔥 2. FALLBACK: 24K × %
        // =============================
        $karat = Karat::find($karatId);

        if (!$karat || !$karat->percentage) {
            throw new \Exception("Kadar karat tidak valid");
        }

        // if (!$goldPrice->price_24k) {
        //     throw new \Exception("Harga 24K tidak tersedia");
        // }

        $harga = $detail->price * ($karat->percentage / 100);

        return round($harga, 0); // 🔥 penting
    }
}
