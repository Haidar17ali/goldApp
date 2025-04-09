<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PO extends Model
{
    protected $fillable = [
        'date',
        'arrival_date',
        'payment_date',
        'po_code',
        'po_type',
        'supplier_id',
        'supplier_type',
        'ppn',
        'dp',
        'status',
        'order_by',
        'created_by',
        'edited_by',
        'edited_by',
        'approved_by',
        'approved_at',
        'activation_date',
    ];

    public function details(){
        return $this->hasMany(PODetails::class, 'po_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function edit_by(){
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function approvedBy(){
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function order_by(){
        return $this->belongsTo(Employee::class, 'order_by');
    }
}
