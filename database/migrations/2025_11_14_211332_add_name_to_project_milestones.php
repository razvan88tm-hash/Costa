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
    Schema::table('project_milestones', function (Blueprint $table) {
        // Verificăm dacă lipsește coloana 'name' și o adăugăm
        if (!Schema::hasColumn('project_milestones', 'name')) {
            $table->string('name')->after('type');
        }
        
        // Verificăm preventiv și 'description', poate lipsește și ea
        if (!Schema::hasColumn('project_milestones', 'description')) {
            $table->text('description')->nullable()->after('name');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
  
{
    Schema::table('project_milestones', function (Blueprint $table) {
        $table->dropColumn(['name', 'description']);
    });
}
};
