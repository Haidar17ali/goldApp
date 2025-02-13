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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('nip');
            $table->integer('pin')->nullable();
            $table->bigInteger('nik');
            $table->bigInteger('no_kk');
            $table->string('fullname');
            $table->string('alias_name');
            $table->boolean('gender');
            $table->integer('address_id')->nullable();
            $table->enum('employee_type', ['Harian', 'Bulanan','Borongan']);
            $table->integer('position_id');
            $table->date('entry_date');
            $table->enum('payment_type', ['ATM', 'Tunai']);
            $table->bigInteger('bank_id')->nullable();
            $table->integer('salary_id');
            $table->integer('premi')->nullable();
            $table->enum('location', ['Bukir Utara', 'Bukir Selatan', 'Kaligung']);
            $table->integer('jkn_number')->nullable();
            $table->integer('jkp_number')->nullable();
            $table->enum('mariage_status', ['Belum kawin', 'Kawin belum tercatat', 'Kawin tercatat', 'Cerai hidup', 'Cerai mati']);
            $table->integer('family_depents');
            $table->date('exit_date')->nullable();
            $table->boolean('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
