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
            $table->dateTime('date');
            $table->dateTime('arrival_date')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->text('po_code');
            $table->enum('po_type', ['Sengon', 'Bahan-Pembantu', 'SPK']);
            $table->bigInteger('supplier_id')->nullable();
            $table->enum('supplier_type', ['Umum', 'Khusus']);
            $table->double('dp')->nullable();
            $table->enum('status', ['Order', 'Datang', 'Terbayar', 'Aktif', "Pending", 'Non-Aktif', 'Tidak Disetujui', 'Gagal']);
            $table->integer('order_by')->nullable();
            $table->integer('created_by');
            $table->integer('edited_by')->nullable();
            $table->integer('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('activation_date')->nullable();
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
