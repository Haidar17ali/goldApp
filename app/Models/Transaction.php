<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'type',
        'purchase_type',
        'branch_id',
        'storage_location_id',
        'transaction_date',
        'invoice_number',
        'total',
        'customer_name',
        'supplier_name',
        'note',
        'created_by',
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
