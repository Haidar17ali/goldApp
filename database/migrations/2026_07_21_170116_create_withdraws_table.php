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
        Schema::create('withdraws', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Marketplace
            |--------------------------------------------------------------------------
            */

            $table->foreignId('marketplace_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Withdraw
            |--------------------------------------------------------------------------
            */

            // Tanggal dana sudah menjadi Kas Online
            $table->date('receive_at')->nullable();

            // Tanggal dana benar-benar masuk rekening
            $table->date('transaction_date')->nullable();

            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->decimal('total', 15, 2)->default(0);

            $table->text('note')->nullable();

            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->timestamps();

            $table->index('receive_at');
            $table->index('transaction_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
