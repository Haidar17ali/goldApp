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
        Schema::table('karats', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->after('name'); // contoh: 75.00 untuk 18K
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karats', function (Blueprint $table) {
            //
        });
    }
};
