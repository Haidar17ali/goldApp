<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldMergeConversionInput extends Model
{
    protected $fillable = [
        'gold_merge_conversion_id',
        'product_variant_id',
        'qty',
        'note'
    ];

    public function conversion()
    {
        return $this->belongsTo(GoldMergeConversion::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
