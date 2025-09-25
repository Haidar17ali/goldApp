<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
     protected $fillable = [
        'product_id',
        'color_id',
        'size_id',
        'sku',
        'barcode',
        'default_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_variant_id');
    }
}
