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
        Schema::create('project_cost_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            $table->string('category')->nullable();
            $table->string('line_item')->nullable();

            $table->decimal('planned_budget', 15, 2)->default(0);
            $table->decimal('committed_po', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->decimal('forecast_to_complete', 15, 2)->default(0);
            $table->decimal('variance_act_plan', 15, 2)->nullable();
            
            $table->decimal('percent_variance', 8, 4)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_cost_items');
    }
};