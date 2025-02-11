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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('npwp_number');
            $table->string('nitku');
            $table->bigInteger('nik');
            $table->enum('supplier_type', ['Sengon', 'Merbau', 'Pembantu']);
            $table->string('name');
            $table->string('phone')->nullable();
            $table->integer('address_id')->nullable();
            $table->integer('bank_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
