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
        Schema::create('cutting_details', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer("cutting_id");
            $table->integer("product_variant_id");
            $table->bigInteger("qty");
            $table->date("finish_at")->nullable();
            $table->enum("status", ["Pending", "Cancel", "Finish"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cutting_details');
    }
};
