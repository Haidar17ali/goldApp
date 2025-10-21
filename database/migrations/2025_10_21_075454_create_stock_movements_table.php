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
        Schema::create('stock_movements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();

        $table->enum('type', [
            'in',         // stok masuk
            'out',        // stok keluar
            'adjustment', // koreksi stok manual
            'loan_out',   // dipinjam influencer
            'loan_in',    // dikembalikan influencer
        ]);

        $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
        $table->foreignId('storage_location_id')->constrained()->cascadeOnDelete();

        $table->decimal('quantity', 12, 3);
        $table->decimal('weight', 12, 3)->nullable(); // jika mau tracking gram emas

        $table->string('reference_type')->nullable(); // contoh: "Purchase", "Sale", "Loan"
        $table->unsignedBigInteger('reference_id')->nullable(); // ID transaksi terkait
        $table->string('note')->nullable();

        $table->foreignId('created_by')->nullable()->constrained('users');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
