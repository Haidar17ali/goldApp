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

    public function lpbs()
    {
        return $this->belongsToMany(Lpb::class, 'purchase_jurnal_lpbs', 'pj_detail_id', 'lpb_id')->withPivot('status');
    }
}
