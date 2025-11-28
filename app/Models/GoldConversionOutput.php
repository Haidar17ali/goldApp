<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldConversionOutput extends Model
{
    protected $fillable = [
        'gold_conversion_id',
        'product_id',
        'karat_id',
        'weight',
        'note',
    ];

    // Header conversion
    public function conversion()
    {
        return $this->belongsTo(GoldConversion::class, 'gold_conversion_id');
    }

    // Produk hasil pecahan (cincin/gelang/kalung)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function kadar()
    {
        return $this->belongsTo(Product::class, 'karat_id');
    }
}
