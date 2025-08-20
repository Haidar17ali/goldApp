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
        Schema::create('wood_management_details', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("wood_management_id");
            $table->bigInteger("lpb_id");
            $table->bigInteger("source_diameter");
            $table->bigInteger("source_qty");
            $table->bigInteger("conversion_diameter");
            $table->bigInteger("conversion_qty");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wood_management_details');
    }
};
