<?php

namespace App\Helpers;

use App\Models\GoldPrice;

class GoldHelper
{
    public static function getHargaByKarat($karatId)
    {
        $goldPrice = GoldPrice::with('details')
            ->whereNull('expired_at')
            ->first();

        if (!$goldPrice) {
            throw new \Exception('Harga emas aktif belum tersedia');
        }

        $detail = $goldPrice->details
            ->where('karat_id', $karatId)
            ->first();

        if (!$detail) {
            throw new \Exception("Harga untuk karat ID {$karatId} tidak ditemukan");
        }

        return $detail->price;
    }
}
