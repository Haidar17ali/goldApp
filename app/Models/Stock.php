<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = [
        'branch_id',
        'storage_location_id',
        'product_variant_id',
        // 'karat_id',
        // 'weight',
        'type',
        'quantity',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(productVariant::class);
    }

    // public function karat()
    // {
    //     return $this->belongsTo(Karat::class);
    // }

    public function conversions()
    {
        return $this->hasMany(GoldConversion::class, 'stock_id');
    }
}
