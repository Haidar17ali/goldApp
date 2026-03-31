<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPriceDetail extends Model
{
    protected $fillable = [
        'gold_price_id',
        'karat_id',
        'price',
    ];

    public function goldPrice()
    {
        return $this->belongsTo(GoldPrice::class);
    }

    public function karat()
    {
        return $this->belongsTo(Karat::class);
    }
}
