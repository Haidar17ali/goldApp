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
        Schema::create('rotaries', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->date("date");
            $table->enum("shift", ["1","2", "3"]);
            $table->text("type");
            $table->text("wood_type")->default("Sengon")->nullable();
            $table->bigInteger("tally_id");
            $table->bigInteger("created_by");
            $table->bigInteger("edited_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rotaries');
    }
};
