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

        if (empty($row['product_name']) || empty($row['gram'])) {
            // Jika data penting kosong, skip baris ini
            return null;
        }

        // === Cari atau buat product ===
        $product = Product::firstOrCreate(
            ['name' => trim($row['product_name'])],
            ['code' => strtoupper(Str::slug($row['product_name'], '-'))] // optional field
        );

        // === Cari atau buat karat (jika diisi) ===
        $karat = null;
        if (!empty($row['karat_name'])) {
            $karat = Karat::firstOrCreate(
                ['name' => trim($row['karat_name'])]
            );
        }

        $karatName = $karat ? $karat->name : 'NOKRT';

        // === Generate SKU & Barcode ===
        $sku = strtoupper($product->name . '-' . $karatName . '-' . $row['gram']);
        $barcode = strtoupper(Str::random(12));

        // === Cegah duplikasi SKU ===
        if (ProductVariant::where('sku', $sku)->exists()) {
            return null;
        }

        // === Buat ProductVariant ===
        return new ProductVariant([
            'product_id'    => $product->id,
            'karat_id'      => $karat?->id,
            'gram'          => $row['gram'],
            'sku'           => $sku,
            'barcode'       => $barcode,
            'default_price' => $row['default_price'] ?? 0,
        ]);
    }
}
