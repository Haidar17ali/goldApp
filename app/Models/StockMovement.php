<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_variant_id',
        'branch_id',
        'storage_location_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'note',
        'created_by',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }
}

