<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // gold_conversions
        Schema::table('gold_conversions', function (Blueprint $table) {

            $table->unsignedBigInteger('branch_id')
                ->nullable()
                ->after('stock_id');
        });

        // gold_merge_conversions
        Schema::table('gold_merge_conversions', function (Blueprint $table) {

            $table->unsignedBigInteger('branch_id')
                ->nullable()
                ->after('id');
        });

        /**
         * ====================================
         * DATA LAMA JADI BRANCH 1
         * ====================================
         */

        DB::table('gold_conversions')
            ->update([
                'branch_id' => 1
            ]);

        DB::table('gold_merge_conversions')
            ->update([
                'branch_id' => 1
            ]);

        /**
         * ====================================
         * JADIKAN NOT NULL
         * ====================================
         */

        DB::statement("
            ALTER TABLE gold_conversions
            MODIFY branch_id BIGINT UNSIGNED NOT NULL
        ");

        DB::statement("
            ALTER TABLE gold_merge_conversions
            MODIFY branch_id BIGINT UNSIGNED NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('gold_conversions', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        Schema::table('gold_merge_conversions', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
