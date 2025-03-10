<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseJurnalDetail extends Model
{
    protected $fillable = [
        'id',
        'pj_id',
        'lpb_id',
        'status',
    ];
}
