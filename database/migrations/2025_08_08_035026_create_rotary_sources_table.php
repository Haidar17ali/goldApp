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
        Schema::create('rotary_sources', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("rotary_id");
            $table->bigInteger("source_id");
            $table->string("source_type");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotary_sources');
    }
};
