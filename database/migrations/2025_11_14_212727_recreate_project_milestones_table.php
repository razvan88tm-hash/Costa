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
        // 1. Ștergem tabela veche și stricată (dacă există)
        Schema::dropIfExists('project_milestones');

        // 2. O creăm din nou, CURAT, cu toate coloanele necesare
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            
            // Legătura cu proiectul
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Coloanele care lipseau
            $table->enum('type', ['pm', 'installation'])->default('pm');
            $table->string('name');
            $table->text('description')->nullable();
            
            // Date
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            
            // Status și Ordine
            $table->enum('status', ['not_started', 'in_progress', 'done'])->default('not_started');
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};