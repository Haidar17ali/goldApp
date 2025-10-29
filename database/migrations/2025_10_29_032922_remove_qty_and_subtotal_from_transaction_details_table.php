<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['qty', 'subtotal']);
        });
    }

    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('qty', 12, 3)->after('gram');
            $table->decimal('subtotal', 18, 2)->after('unit_price');
        });
    }
};
