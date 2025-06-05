<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'lpb_id',
        'type',
    ];

    public function lpb(){
        return $this->belongsTo(LPB::class, 'lpb_id');
    }
}
