<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // număr proiect (ex: 2412)
            $table->string('project_number', 50)->nullable()->after('id');

            // nume client (ex: Kleymann)
            $table->string('client_name')->nullable()->after('project_number');

            // locație client (ex: City, Country)
            $table->string('location')->nullable()->after('customer_site');

            // data handover reală
            $table->date('real_handover')->nullable()->after('target_handover');

            // link către ultimul layout PDF din 03_Layout (deocamdată manual)
            $table->string('layout_file_url')->nullable()->after('drive_folder_url');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_number',
                'client_name',
                'location',
                'real_handover',
                'layout_file_url',
            ]);
        });
    }
};