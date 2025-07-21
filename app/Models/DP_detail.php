<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DP_detail extends Model
{
    protected $fillable = [
            'dp_id',
            'nopol',
            'length',
            'qty',
            'cubication',
            'price',
    ];

    public function DP(){
        return $this->belongsTo(Down_payment::class, 'dp_id');
    }
}
