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
        Schema::create('wood_management', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->date("date");
            $table->bigInteger("no_kitir");
            $table->enum("grade", ["Super", "Afkir"])->default("Super");
            $table->enum("type", ["Afkir","Upgrade", "Pending", "Downgrade"])->default("Pending");
            $table->enum("from", ["260", "130"])->default("260");
            $table->enum("to", ["260", "130"])->default("130");
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
        Schema::dropIfExists('wood_management');
    }
};
