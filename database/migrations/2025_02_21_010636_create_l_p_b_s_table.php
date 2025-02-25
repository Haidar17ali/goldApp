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
        Schema::create('l_p_b_s', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('po_id');
            $table->bigInteger('road_permit_id');
            $table->bigInteger('no_kitir');
            $table->string('nopol'); //dapat dari surat jalan satpam
            $table->date('lpb_date');
            $table->bigInteger('supplier_id');
            $table->bigInteger('npwp_id'); //otomatis berdasarkan supplier
            $table->bigInteger('grader_id');
            $table->bigInteger('tally_id');
            $table->bigInteger('created_by');//pembuat
            $table->bigInteger('edited_by')->nullable();//pembuat
            $table->bigInteger('approval_by')->nullable();// yang menyetuji
            $table->date('payment_date')->nullable();
            $table->bigInteger('conversion')->nullable(); //untuk menghitung total potongan * harga potongan
            $table->enum('status', ['Sukses', 'Pending', 'Tolak']); //jika status sukses maka bisa dibayarkan jika pending masih belum
            $table->text('address')->nullable(); //Alamat Untuk Sppt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('l_p_b_s');
    }
};
