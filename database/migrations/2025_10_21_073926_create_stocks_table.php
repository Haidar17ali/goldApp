<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('karat_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight', 12, 3);
            $table->enum('type', ['new', 'sepuh', 'rosok'])->default('new');

            $table->decimal('quantity', 15, 3)->default(0); // dalam gram, bisa pecahan
            $table->timestamps();

            $table->unique(['branch_id', 'storage_location_id', 'product_id', "karat_id", "weight", "type"], 'unique_stock_per_location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
