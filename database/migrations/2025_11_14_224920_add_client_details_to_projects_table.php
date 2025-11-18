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
    Schema::table('projects', function (Blueprint $table) {
        $table->string('client_contact_person')->nullable()->after('client_name');
        $table->string('client_phone')->nullable()->after('client_contact_person');
        $table->string('client_email')->nullable()->after('client_phone');
        // Adăugăm și câmpul pentru link-ul de oferte (Folder 04)
        $table->string('offers_folder_url')->nullable()->after('layout_file_url');
    });
}

public function down(): void
{
    Schema::table('projects', function (Blueprint $table) {
        $table->dropColumn(['client_contact_person', 'client_phone', 'client_email', 'offers_folder_url']);
    });
}
};
