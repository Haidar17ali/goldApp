<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseJurnal extends Model
{
    protected $fillable =[
        'pj_code',
        'date',
        'created_by',
        'edited_by',
        'status',
    ];

    public function details(){
        return $this->hasMany(PurchaseJurnalDetail::class, 'pj_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lpbs()
    {
        return $this->belongsToMany(Lpb::class, 'purchase_jurnal_lpbs', 'pj_detail_id', 'lpb_id');
    }

}
