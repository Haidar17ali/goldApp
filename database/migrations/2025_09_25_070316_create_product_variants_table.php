<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('karat_id')->nullable()->constrained()->nullOnDelete()->cascadeOnDelete();

            $table->double('gram')->nullable();
            $table->enum('type', ["sepuh", "new", "customer", "batangan"])->default("new");
            $table->string('sku')->unique();      // SKU unik
            $table->string('barcode')->nullable()->unique(); // bisa null kalau belum ada
            $table->integer('default_price')->nullable();

            $table->unique(['product_id', 'karat_id', 'gram', 'type']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();
    }
};
