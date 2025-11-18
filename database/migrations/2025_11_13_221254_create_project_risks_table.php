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
          Schema::create('project_risks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('project_id')->constrained()->onDelete('cascade');

        $table->string('title');                // Descrierea riscului
        $table->string('impact')->nullable();   // High / Medium / Low
        $table->string('probability')->nullable(); // High / Medium / Low
        $table->string('owner')->nullable();    // Cine e responsabil
        $table->date('due_date')->nullable();   // Termen
        $table->string('status')->nullable();   // Open / In progress / Closed etc.

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_risks');
    }
};
