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
        Schema::create('l_p_b_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('lpb_id');
            $table->string("product_code");
            $table->integer("length");
            $table->integer("diameter");
            $table->integer("price");
            $table->integer("qty");
            $table->enum("quality", ["Afkir", "Super"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('l_p_b_details');
    }
};
