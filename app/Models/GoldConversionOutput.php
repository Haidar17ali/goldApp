<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldConversionOutput extends Model
{
    protected $fillable = [
        'gold_conversion_id',
        'product_variant_id',
        'weight',
        'note',
    ];

    // Header conversion
    public function conversion()
    {
        return $this->belongsTo(GoldConversion::class, 'gold_conversion_id');
    }

    // Produk hasil pecahan (cincin/gelang/kalung)
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
