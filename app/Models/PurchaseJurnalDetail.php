<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseJurnalDetail extends Model
{
    protected $fillable = [
        'id',
        'pengajuan_id',
        'type',
        'status',
    ];
}
