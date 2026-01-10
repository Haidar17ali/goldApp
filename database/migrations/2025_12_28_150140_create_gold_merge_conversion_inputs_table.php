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
        Schema::create('gold_merge_conversion_inputs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gold_merge_conversion_id');
            $table->unsignedBigInteger('product_id'); // emas (product 7)
            $table->unsignedBigInteger('karat_id');
            $table->decimal('weight', 12, 3);
            $table->integer('qty');
            $table->text('note')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_merge_conversion_inputs');
    }
};
