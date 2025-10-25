<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            // decimal(15,3) cocok untuk gram
            $table->decimal('weight', 15, 3)->default(0)->after('difference');
        });
    }

    public function down(): void {
        Schema::table('stock_adjustment_details', function (Blueprint $table) {
            $table->dropColumn('weight');
        });
    }
};

