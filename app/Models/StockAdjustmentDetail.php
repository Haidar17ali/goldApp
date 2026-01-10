<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentDetail extends Model
{
    protected $fillable = [
        'stock_adjustment_id', 'product_variant_id',
        'system_qty', 'actual_qty', 'difference', 'type', "weight"
    ];

    public function adjustment() {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function productVariant() {
        return $this->belongsTo(ProductVariant::class);
    }

    // public function karat() {
    //     return $this->belongsTo(Karat::class);
    // }
}
