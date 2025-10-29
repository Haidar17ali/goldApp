<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'branch_id', 'storage_location_id', 'adjustment_date',
        'note', 'created_by', 'approved_by', 'approved_at', "weight"
    ];
    // app/Models/StockAdjustment.php
protected $casts = [
    'adjustment_date' => 'datetime',
];


    public function details() {
        return $this->hasMany(StockAdjustmentDetail::class);
    }

    public function branch() {
        return $this->belongsTo(Branch::class);
    }

    public function storageLocation() {
        return $this->belongsTo(StorageLocation::class);
    }
}

