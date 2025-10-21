<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_variant_id',
        'qty',
        'unit_price',
        'subtotal',
        'note',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
