<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldMergeConversionInput extends Model
{
    protected $fillable = [
        'gold_merge_conversion_id',
        'product_id',
        'karat_id',
        'weight',
        'qty',
        'note'
    ];

    public function conversion()
    {
        return $this->belongsTo(GoldMergeConversion::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
