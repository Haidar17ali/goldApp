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
        Schema::create('purchase_jurnals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('pu_code');
            $table->date('date');
            $table->bigInteger('created_by');
            $table->bigInteger('edited_by');
            $table->enum('status', ['Proses', 'Selesai']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_jurnals');
    }
};
