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
        Schema::create('p_o_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('po_date');
            $table->dateTime('arrival_date')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->text('po_code');
            $table->enum('po_type', ['Bahan-Baku', 'Bahan-Pembantu', 'SPK']);
            $table->bigInteger('supplier_id');
            $table->enum('supplier_type', ['Umum', 'Khusus']);
            $table->double('ppn')->nullable();
            $table->double('dp')->nullable();
            $table->enum('status', ['Order', 'Datang', 'Terbayar', 'Aktif', 'Non-Aktif']);
            $table->integer('order_by')->nullable();
            $table->integer('created_by');
            $table->integer('edited_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_o_s');
    }
};
