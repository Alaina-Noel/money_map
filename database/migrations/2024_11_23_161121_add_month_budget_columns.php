<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration {
    public function up(): void
    {
        // First add nullable columns
        Schema::table('categories', function (Blueprint $table) {
            $table->date('budget_month')->nullable()->after('user_id');
        });

        Schema::table('line_items', function (Blueprint $table) {
            $table->date('budget_month')->nullable()->after('user_id');
        });

        Schema::table('budget_summaries', function (Blueprint $table) {
            $table->date('budget_month')->nullable()->after('user_id');
        });

        // Update existing records to use current month
        DB::table('categories')->update([
            'budget_month' => Carbon::now()->startOfMonth()
        ]);

        DB::table('line_items')->update([
            'budget_month' => Carbon::now()->startOfMonth()
        ]);

        DB::table('budget_summaries')->update([
            'budget_month' => Carbon::now()->startOfMonth()
        ]);

        // Now make the columns non-nullable
        Schema::table('categories', function (Blueprint $table) {
            $table->date('budget_month')->nullable(false)->change();
        });

        Schema::table('line_items', function (Blueprint $table) {
            $table->date('budget_month')->nullable(false)->change();
        });

        Schema::table('budget_summaries', function (Blueprint $table) {
            $table->date('budget_month')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('budget_month');
        });

        Schema::table('line_items', function (Blueprint $table) {
            $table->dropColumn('budget_month');
        });

        Schema::table('budget_summaries', function (Blueprint $table) {
            $table->dropColumn('budget_month');
        });
    }
};
