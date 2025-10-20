<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
     protected $fillable = [
        'product_id',
        'karat_id',
        'gram',
        'sku',
        'barcode',
        'default_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function karat()
    {
        return $this->belongsTo(Karat::class);
    }
}
