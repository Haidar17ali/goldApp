<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Karat;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductVariantImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan kolom di Excel punya heading:
        // product_name | karat_name | gram | default_price

        // Cari product berdasarkan nama
        $product = Product::where('name', $row['product_name'])->first();

        if (! $product) {
            // Jika product tidak ditemukan, skip baris ini
            return null;
        }

        $karat = null;
        if (!empty($row['karat_name'])) {
            $karat = Karat::where('name', $row['karat_name'])->first();
        }

        $karatName = $karat ? $karat->name : 'NOKRT';

        // Generate SKU & Barcode
        $sku = strtoupper($product->name . '-' . $karatName . '-' . $row['gram']);
        $barcode = strtoupper(Str::random(12));

        // Cegah duplikasi (jika SKU sudah ada)
        $existing = ProductVariant::where('sku', $sku)->first();
        if ($existing) return null;

        return new ProductVariant([
            'product_id'    => $product->id,
            'karat_id'      => $karat ? $karat->id : null,
            'gram'          => $row['gram'],
            'sku'           => $sku,
            'barcode'       => $barcode,
            'default_price' => $row['default_price'] ?? 0,
        ]);
    }
}
