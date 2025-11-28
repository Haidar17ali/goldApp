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
        Schema::create('gold_conversion_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gold_conversion_id')->constrained('gold_conversions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict'); // cincin/gelang
            $table->unsignedBigInteger('karat_id'); // tetap 8K
            $table->decimal('weight', 10, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_conversion_outputs');
    }
};
