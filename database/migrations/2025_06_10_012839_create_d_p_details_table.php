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
        Schema::create('d_p_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dp_id');
            $table->text('nopol')->nullable();
            $table->integer('length')->nullable();
            $table->bigInteger('qty');
            $table->double('cubication')->nullable();
            $table->bigInteger('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_p_details');
    }
};
