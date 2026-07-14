<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_stocks', function (Blueprint $table) {

            $table->id();

            $table->date('transfer_date');

            $table->foreignId('from_branch_id')
                ->constrained('branches');

            $table->foreignId('to_branch_id')
                ->constrained('branches');

            $table->text('note')->nullable();

            $table->enum('status', [
                'draft',
                'sent',
                'received',
                'cancelled'
            ])->default('draft');

            $table->foreignId('created_by')
                ->constrained('users');
            $table->foreignId('edited_by')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_stocks');
    }
};
