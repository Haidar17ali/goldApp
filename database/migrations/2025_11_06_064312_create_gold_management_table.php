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
        Schema::create('gold_management', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->date("date"); //tanggal pengelolaan
            $table->enum("type", ['sepuh','patri','rosok']);
            $table->integer("product_id"); 
            $table->integer("karat_id"); 
            $table->double("gram_in"); 
            $table->double("gram_out"); 
            $table->text("note")->nullable(); 
            $table->integer("created_by"); 
            $table->integer("edited_by")->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_management');
    }
};
