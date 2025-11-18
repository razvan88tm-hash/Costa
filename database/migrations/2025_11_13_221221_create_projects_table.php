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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->nullable();
            $table->string('customer_site')->nullable();
            $table->string('pm_name')->nullable();

            $table->enum('overall_rog', ['Green', 'Orange', 'Red'])->default('Green');
            $table->decimal('percent_complete', 5, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->date('target_handover')->nullable();

           $table->decimal('sell_price', 15, 2)->nullable();
           $table->decimal('planned_budget', 15, 2)->nullable();
           $table->decimal('committed_pos', 15, 2)->nullable();
           $table->decimal('actuals_to_date', 15, 2)->nullable();
           $table->decimal('forecast_to_complete', 15, 2)->nullable();
           $table->decimal('actual_profit', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};