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
        Schema::create('p_o_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('po_id');
            $table->string('name')->nullable();
            $table->double('diameter_start')->nullable();
            $table->double('diameter_to')->nullable();
            $table->double('quantity')->nullable();
            $table->double('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_o_details');
    }
};
