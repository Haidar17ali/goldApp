<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LPBDetail extends Model
{
   protected $fillable = [
        'lpb_id',
        'product_code',
        'length',
        'diameter',
        'qty',
        'price',
        'quality',
    ];
}
