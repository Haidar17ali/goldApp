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
        Schema::create('withdraw_details', function (Blueprint $table) {

            $table->id();

            $table->foreignId('withdraw_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('transaction_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->timestamps();

            // satu transaksi hanya boleh masuk satu withdraw
            $table->unique('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_details');
    }
};
