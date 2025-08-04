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
        Schema::table('daily_sales', function (Blueprint $table) {
            // First rename the existing column
            $table->renameColumn('dough_remaining', 'dough_remaining_printed');

            // Add new columns
            $table->integer('dough_remaining_unprinted')->default(0)->comment('Amount of remaining unprinted dough');
            $table->decimal('employee_expenses', 10, 2)->default(0)->comment('Money spent by employee during sales');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_sales', function (Blueprint $table) {
            $table->renameColumn('dough_remaining_printed', 'dough_remaining');
            $table->dropColumn(['dough_remaining_unprinted', 'employee_expenses']);
        });
    }
};
