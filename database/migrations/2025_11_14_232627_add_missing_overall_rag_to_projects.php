<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Adăugăm coloana doar dacă nu există deja
            if (!Schema::hasColumn('projects', 'overall_rag')) {
                // Definim coloana cu valorile pentru ROG (Red, Orange, Green)
                $table->enum('overall_rag', ['Red', 'Orange', 'Green'])
                      ->default('Green')
                      ->after('pm_name'); // O punem după numele PM-ului, să fie ordonat
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'overall_rag')) {
                $table->dropColumn('overall_rag');
            }
        });
    }
};