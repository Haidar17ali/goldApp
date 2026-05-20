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
        Schema::create('cash_mutations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reference')->unique();

            $table->foreignId('from_bank_account_id')->nullable()->constrained('bank_accounts');
            $table->foreignId('to_bank_account_id')->nullable()->constrained('bank_accounts');

            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_mutations');
    }
};
