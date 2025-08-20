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
        Schema::create('rotary_details', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("rotary_id");
            $table->bigInteger("no_kitir");
            $table->double("height");
            $table->double("width");
            $table->double("length");
            $table->double("qty");
            $table->text("grade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotary_details');
    }
};
