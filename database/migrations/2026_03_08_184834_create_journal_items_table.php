<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_items', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('journal_id');

            $table->unsignedBigInteger('chart_of_account_id');

            $table->decimal('debit', 18, 2)->default(0);

            $table->decimal('credit', 18, 2)->default(0);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->foreign('journal_id')
                ->references('id')
                ->on('journals')
                ->cascadeOnDelete();

            $table->foreign('chart_of_account_id')
                ->references('id')
                ->on('chart_of_accounts');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_items');
    }
};
