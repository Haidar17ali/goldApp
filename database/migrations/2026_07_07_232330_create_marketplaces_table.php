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
        Schema::create('marketplaces', function (Blueprint $table) {

            $table->id();

            // Nama marketplace
            $table->string('name');

            // Kode unik
            $table->string('code')->unique();

            // Logo marketplace
            $table->string('logo')->nullable();

            // Deskripsi
            $table->text('description')->nullable();

            // Urutan tampilan
            $table->unsignedInteger('sort_order')->default(0);

            // Status aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplaces');
    }
};
