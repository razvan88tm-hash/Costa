<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            // Adăugăm coloana doar dacă nu există deja
            if (!Schema::hasColumn('project_milestones', 'type')) {
                $table->enum('type', ['pm', 'installation'])
                      ->default('pm')
                      ->after('project_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            if (Schema::hasColumn('project_milestones', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
