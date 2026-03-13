<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nominal
            $table->decimal('gaji', 15, 2)->default(0);
            $table->decimal('potongan', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);

            // Sistem pembayaran
            $table->enum('sistem_gaji', ['tunai', 'tf']);

            // Hari kerja
            $table->integer('hari_kerja')->default(0);

            // Optional: periode gaji (SANGAT DISARANKAN)
            $table->date('periode')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
