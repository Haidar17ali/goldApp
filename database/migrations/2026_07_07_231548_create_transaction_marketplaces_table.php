<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_marketplaces', function (Blueprint $table) {

            $table->id();

            // Relasi transaksi
            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Marketplace
            |--------------------------------------------------------------------------
            */
            $table->foreignId('marketplace_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('shop_name')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Informasi Order
            |--------------------------------------------------------------------------
            */
            $table->string('order_id')->nullable()->index();
            $table->string('invoice_number')->nullable();
            $table->string('tracking_number')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengiriman
            |--------------------------------------------------------------------------
            */
            $table->string('courier')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->string('order_status')->nullable();
            $table->string('payment_status')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Snapshot Customer
            |--------------------------------------------------------------------------
            */
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->text('shipping_address')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Nominal
            |--------------------------------------------------------------------------
            */
            $table->decimal('subtotal', 15, 2)->nullable()->default(0);

            $table->decimal('shipping_fee', 15, 2)->nullable()->default(0);

            $table->decimal('discount_amount', 15, 2)->nullable()->default(0);

            $table->decimal('admin_fee', 15, 2)->nullable()->default(0);

            $table->decimal('service_fee', 15, 2)->nullable()->default(0);

            $table->decimal('other_fee', 15, 2)->nullable()->default(0);

            // Total yang dibayar customer
            $table->decimal('marketplace_total', 15, 2)->default(0);

            // Dana bersih yang diterima
            $table->decimal('received_amount', 15, 2)->default(0);

            /*
            |--------------------------------------------------------------------------
            | Settlement
            |--------------------------------------------------------------------------
            */
            $table->dateTime('settlement_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Integrasi API
            |--------------------------------------------------------------------------
            */
            $table->timestamp('synced_at')->nullable();

            // Response asli API marketplace
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index('order_status');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_marketplaces');
    }
};
