<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('type_id')
                ->nullable()
                ->constrained('types')
                ->nullOnDelete();

            $table->foreignId('gram_id')
                ->nullable()
                ->constrained('grams')
                ->nullOnDelete();

            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->integer('default_price')->nullable();
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
