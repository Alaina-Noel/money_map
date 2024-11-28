<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create budgets table (replaces budget_summaries)
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('budget_month');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'budget_month']);
        });

        // Create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('budget_month');
            $table->string('name');
            $table->decimal('expected_amount', 10, 2);
            $table->decimal('actual_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'budget_month']);
            // Remove unique constraint on name since it should be unique per user and month
            $table->unique(['user_id', 'budget_month', 'name']);
        });

        // Create line_items table
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 10, 2);
            $table->date('date');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'date']);
        });

        // Create paychecks table
        Schema::create('paychecks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('pay_date');
            $table->decimal('amount', 10, 2);
            $table->string('source')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'pay_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('line_items');
        Schema::dropIfExists('paychecks');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('budgets');
    }
};
