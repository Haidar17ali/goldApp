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
        Schema::create('road_permit_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('road_permit_id');
            $table->string('load');
            $table->integer('amount');
            $table->enum('unit', ['Batang', 'Palet', 'Sak', 'Liter','Rit', 'Box',"Pcs", 'Drum', 'Bandel', 'Box/Galon']);
            $table->string('size')->nullable();
            $table->float('cubication')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('road_permit_details');
    }
};
