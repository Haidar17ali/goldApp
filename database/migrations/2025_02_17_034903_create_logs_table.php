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
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('id_produksi')->nullable();
            $table->text('barcode')->nullable();
            $table->text('code');
            $table->enum('type', ['Sengon', 'Merbau']);
            $table->enum('quality', ['Super', 'Afkir']);
            $table->float('length');
            $table->float('diameter');
            $table->float('quantity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
