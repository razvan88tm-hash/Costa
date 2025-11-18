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
        // Aici vom salva ID-ul folderului LESTA (ex: 1a2b3c_...)
        $table->string('drive_lesta_folder_id')->nullable()->after('transport_per_km');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            //
        });
    }
};
