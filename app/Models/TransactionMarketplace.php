<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionMarketplace extends Model
{
    protected $fillable = [
        'transaction_id',

        // Marketplace
        'marketplace_id',
        'shop_name',

        // Identitas Order
        'order_id',
        'invoice_number',
        'tracking_number',
        'courier',

        // Status
        'order_status',
        'payment_status',

        // Customer Snapshot
        'buyer_name',
        'buyer_phone',
        'shipping_address',

        // Nominal
        'subtotal',
        'shipping_fee',
        'discount_amount',
        'admin_fee',
        'service_fee',
        'other_fee',
        'marketplace_total',
        'received_amount',

        // Settlement
        'settlement_at',

        // API
        'synced_at',
        'payload',
    ];

    protected $casts = [
        'payload'        => 'array',
        'settlement_at'  => 'datetime',
        'synced_at'      => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function marketplace()
    {
        return $this->belongsTo(Marketplace::class);
    }
}
