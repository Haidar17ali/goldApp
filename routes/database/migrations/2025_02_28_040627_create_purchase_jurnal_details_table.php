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
        Schema::create('purchase_jurnal_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('pj_id');
            $table->enum('status', ['Pending','Terbayar', 'Gagal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_jurnal_details');
    }
};
