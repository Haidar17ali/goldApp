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
        Schema::create('gold_price_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_price_id')->constrained()->cascadeOnDelete();
            $table->foreignId('karat_id')->constrained();
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_price_details');
    }
};
