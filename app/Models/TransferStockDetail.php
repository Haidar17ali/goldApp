<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferStockDetail extends Model
{
    protected $fillable = [
        'transfer_stock_id',
        'product_variant_id',
        'qty',
    ];

    public function transferStock()
    {
        return $this->belongsTo(TransferStock::class, 'transfer_stock_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
