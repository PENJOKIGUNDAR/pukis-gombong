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
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('sale_date');
            $table->integer('dough_brought')->comment('Amount of dough brought');
            $table->integer('dough_remaining')->comment('Amount of remaining dough');
            $table->decimal('total_sales', 10, 2)->comment('Total sales amount');
            $table->decimal('admin_share', 10, 2)->comment('Share for admin');
            $table->decimal('employee_share', 10, 2)->comment('Share for employee');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales');
    }
};
