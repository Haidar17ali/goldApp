<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['purchase', 'penjualan']); // pembelian / penjualan
            $table->enum('purchase_type', ['sepuh', 'pabrik', 'rosok', "new"])->nullable(); // hanya berlaku jika type=purchase
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_location_id')->nullable()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->string('invoice_number')->unique();
            $table->decimal('total', 18, 2)->default(0);
            $table->string('customer_name')->nullable();
            $table->string('supplier_name')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

