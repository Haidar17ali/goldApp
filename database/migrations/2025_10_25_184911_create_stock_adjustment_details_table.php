<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_adjustment_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('karat_id')->nullable();
            $table->decimal('system_qty', 15, 3)->default(0);
            $table->decimal('actual_qty', 15, 3)->default(0);
            $table->decimal('difference', 15, 3)->default(0);
            $table->string('type')->nullable(); // new / sepuh / rosok
            $table->timestamps();

            $table->foreign('stock_adjustment_id')->references('id')->on('stock_adjustments')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('karat_id')->references('id')->on('karats')->cascadeOnDelete();
        });
    }

    public function down(): void {
        Schema::dropIfExists('stock_adjustment_details');
    }
};
