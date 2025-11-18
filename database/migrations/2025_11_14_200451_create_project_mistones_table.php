<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cream tabela DOAR dacă NU există deja
        if (!Schema::hasTable('project_milestones')) {
            Schema::create('project_milestones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->onDelete('cascade');

                $table->enum('type', ['pm', 'installation'])->default('pm');
                $table->string('name');
                $table->text('description')->nullable();
                $table->date('planned_date')->nullable();
                $table->date('actual_date')->nullable();
                $table->enum('status', ['not_started', 'in_progress', 'done'])
                      ->default('not_started');
                $table->unsignedInteger('sort_order')->default(0);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('project_milestones')) {
            Schema::dropIfExists('project_milestones');
        }
    }
};

