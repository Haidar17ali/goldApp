<?php

// namespace App\Helpers;

// use App\Models\GoldPrice;

// class GoldHelper
// {
//     public static function getHargaByKarat($karatId)
//     {
//         $goldPrice = GoldPrice::with('details')
//             ->whereNull('expired_at')
//             ->first();

//         if (!$goldPrice) {
//             throw new \Exception('Harga emas aktif belum tersedia');
//         }

//         $detail = $goldPrice->details
//             ->where('karat_id', $karatId)
//             ->first();

//         if (!$detail) {
//             throw new \Exception("Harga untuk karat ID {$karatId} tidak ditemukan");
//         }

//         return $detail->price;
//     }
// }


namespace App\Helpers;

use App\Models\GoldPrice;

class GoldHelper
{
    public static function getHargaByKarat($karatId)
    {
        // 🔥 ambil harga emas yang benar-benar aktif
        $goldPrice = GoldPrice::where(function ($q) {
            $q->whereNull('expired_at')
                ->orWhere('expired_at', '>=', now());
        })
            ->where('active_at', '<=', now())
            ->orderByDesc('active_at') // ambil yang terbaru
            ->first();

        if (!$goldPrice) {
            throw new \Exception('Harga emas aktif belum tersedia');
        }

        // 🔥 ambil detail langsung dari DB (lebih efisien)
        $detail = $goldPrice->details()
            ->where('karat_id', $karatId)
            ->first();

        if (!$detail) {
            throw new \Exception("Harga untuk karat ID {$karatId} tidak ditemukan");
        }

        return $detail->price;
    }
}
