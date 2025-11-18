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
        Schema::table('project_worker_logs', function (Blueprint $table) {
            // dacă nu există deja coloana, o adăugăm
            if (!Schema::hasColumn('project_worker_logs', 'transport_km')) {
                $table->decimal('transport_km', 8, 2)->default(0)->after('hours_on_site');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_worker_logs', function (Blueprint $table) {
            if (Schema::hasColumn('project_worker_logs', 'transport_km')) {
                $table->dropColumn('transport_km');
            }
        });
    }
};
