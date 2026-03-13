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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nip')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('status')->nullable();

            $table->string('no_hp')->nullable();

            $table->string('no_rek')->nullable();
            $table->string('nama_bank')->nullable();

            $table->decimal('gaji', 15, 2)->nullable();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
