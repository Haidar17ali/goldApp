<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'karat_id',
        'gram',
        'qty',
        'unit_price',
        'subtotal',
        'type',
        'note',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function karat()
    {
        return $this->belongsTo(Karat::class);
    }
}
