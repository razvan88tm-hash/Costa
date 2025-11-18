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
        Schema::table('app_settings', function (Blueprint $table) {
            // Verificăm dacă coloana NU există, și doar atunci o adăugăm
            if (!Schema::hasColumn('app_settings', 'drive_projects_folder_id')) {
                $table->string('drive_projects_folder_id')->nullable()->after('transport_per_km');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nu este nevoie neapărat să o ștergem la rollback, dar e bine să fie
        Schema::table('app_settings', function (Blueprint $table) {
            if (Schema::hasColumn('app_settings', 'drive_projects_folder_id')) {
                $table->dropColumn('drive_projects_folder_id');
            }
        });
    }
};