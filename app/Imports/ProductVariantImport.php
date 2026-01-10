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
        // wajib ada
        if (empty($row['product_name']) || empty($row['weight'])) {
            return null;
        }
        
        // === Product ===
        $product = Product::firstOrCreate(
            ['name' => trim($row['product_name'])],
            ['code' => strtoupper(Str::slug($row['product_name'], '-'))]
        );
        
        // === Karat (optional) ===
        $karat = null;
        if (!empty($row['karat_name'])) {
            $karat = Karat::firstOrCreate([
                'name' => trim($row['karat_name'])
            ]);
        }
        
        $karatName = $karat ? $karat->name : 'NOKRT';
        
        // === Type (new / sepuh) ===
        $type = 'new'; // default
        if (!empty($row['type'])) {
            $rowType = strtolower(trim($row['type']));
            if (in_array($rowType, ['new', 'sepuh'])) {
                $type = $rowType;
            }
        }
        
        // === SKU & Barcode ===
        $sku = strtoupper($product->name . '-' . $karatName . '-' . $row['weight'] . '-' . $type);
        $barcode = strtoupper(Str::random(12));


        // === Cegah duplikasi SKU ===
        if (ProductVariant::where('sku', $sku)->exists()) {
            return null;
        }

        // === Simpan ===
        return new ProductVariant([
            'product_id'    => $product->id,
            'karat_id'      => $karat?->id,
            'gram'          => $row['weight'],
            'type'          => $type, // 🔥 INI YANG BARU
            'sku'           => $sku,
            'barcode'       => $barcode,
            'default_price' => $row['default_price'] ?? 0,
        ]);
    }
}
