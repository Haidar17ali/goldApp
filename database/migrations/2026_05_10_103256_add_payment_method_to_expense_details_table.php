<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_details', function (Blueprint $table) {

            $table->string('payment_type')->default('cash');

            $table->foreignId('bank_account_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expense_details', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};