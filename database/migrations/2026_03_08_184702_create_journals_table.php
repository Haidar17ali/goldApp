<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->date('date');

            $table->string('reference')->nullable();
            // contoh: INV001

            $table->text('description')->nullable();

            $table->string('source_type')->nullable();
            // sale, purchase, payroll, adjustment

            $table->unsignedBigInteger('source_id')->nullable();
            // id transaksi asal

            $table->unsignedBigInteger('reversal_of')->nullable();
            $table->boolean('is_reversal')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();

            // | field       | fungsi                   |
            // | ----------- | ------------------------ |
            // | reversal_of | jurnal mana yang dibalik |
            // | is_reversal | penanda jurnal pembalik  |
            // | created_by  | user yang membuat        |


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
