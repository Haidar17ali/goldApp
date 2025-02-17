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
        Schema::create('road_permits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->time('in');
            $table->dateTime('out')->nullable();
            $table->text('description')->nullable();
            $table->integer('handyman_id')->nullable();
            $table->string('from');
            $table->string('destination');
            $table->enum('vehicle', ['Pickup', 'Truk Engkel', 'Dump Truk', 'Truk Gandeng', 'Truk Fuso', 'Container']);
            $table->string('nopol');
            $table->string('driver');
            $table->string('unpack_location')->nullable();
            $table->string('sill_number')->nullable();
            $table->string('container_number')->nullable();
            $table->enum('type', ['In', 'Out']);
            $table->enum('type_item', ['Sengon', "Merbau", 'Pembantu'])->nullable();
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
        Schema::dropIfExists('road_permits');
    }
};
