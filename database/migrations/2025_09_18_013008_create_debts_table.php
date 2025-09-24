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
        Schema::create('debts', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("tailor");
            $table->integer("source_id");
            $table->integer("cutting_detail_id");
            $table->integer("qty");
            $table->enum("from", ["cutting", "delivery"])->default("cutting");
            $table->enum("status", ["belum", "sebagian", "lunas"])->default("belum");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
