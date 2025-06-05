<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseJurnalLpb extends Model
{
    protected $fillable = [
        'pj_detail_id',
        'lpb_id',
        'pajak',
        'status'
    ];

    public function lpbs()
    {
        return $this->belongsToMany(Lpb::class, 'purchase_jurnal_lpb', 'pj_detail_id', 'lpb_id', 'status')->withTimestamps();
    }

    public function purchaseJurnalDetails()
    {
        return $this->belongsToMany(PurchaseJurnalDetail::class, 'purchase_jurnal_lpb', 'lpb_id', 'pj_detail_id')->withTimestamps();
    }
}
