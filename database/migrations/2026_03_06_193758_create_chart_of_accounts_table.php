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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("code")->unique();
            $table->string("name");
            $table->enum("category", ["asset", "liability", "equity", "revenue", "expense"]);
            $table->enum("normal_balance", ["debit", "credit"]);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean("is_active")->default(true);

            $table->foreign('parent_id')
                ->references('id')
                ->on('chart_of_accounts')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
