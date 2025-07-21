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
        Schema::create('down_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('supplier_id');
            $table->date('date');
            $table->date('nota_date');
            $table->date('arrival_date')->nullable();
            $table->enum('type', ['Sengon', 'Merbau', 'Pembantu']);
            $table->enum('dp_type', ['DP', 'Pelunasan']);
            $table->bigInteger('nominal');
            $table->bigInteger('parent_id')->nullable();
            // $table->enum('status', ['Pending', 'Menunggu Pembayaran', 'Gagal', 'Sukses', 'Selesai']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('down_payments');
    }
};
