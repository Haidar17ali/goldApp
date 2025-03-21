<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LPB extends Model
{
    protected $fillable = [
        'code',
        'po_id',
        'road_permit_id',
        'no_kitir',
        'nopol',
        'lpb_date',
        'supplier_id',
        'npwp_id',
        'grader_id',
        'tally_id',
        'used',
        'used_at',
        'perhutani',
        'created_by',
        'edited_by',
        'approved_by',
        'approved_at',
        'conversion',
        'status',
        'address',
    ];

    public function details(){
        return $this->hasMany(LPBDetail::class, 'lpb_id');
    }

    public function roadPermit(){
        return $this->belongsTo(RoadPermit::class, 'road_permit_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editedBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvalBy(){
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function grader(){
        return $this->belongsTo(Employee::class, 'grader_id');
    }

    public function tally(){
        return $this->belongsTo(Employee::class, 'tally_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function npwp(){
        return $this->belongsTo(npwp::class, 'npwp_id');
    }

    // public function lpbs()
    // {
    //     return $this->hasMany(PurchaseJurnalLpb::class, 'lpb_id', 'id');
    // }

    public function DP(){
        return $this->hasOne(Down_payment::class, 'supplier_id', 'supplier_id')->where('type', 'Out');
    }
}
