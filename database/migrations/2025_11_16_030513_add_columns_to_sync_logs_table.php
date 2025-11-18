<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sync_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('sync_logs', 'status')) {
                $table->string('status')->default('info');
            }

            if (!Schema::hasColumn('sync_logs', 'source')) {
                $table->string('source')->nullable()->after('status');
            }

            if (!Schema::hasColumn('sync_logs', 'message')) {
                $table->text('message')->nullable()->after('source');
            }

            if (!Schema::hasColumn('sync_logs', 'context')) {
                $table->json('context')->nullable()->after('message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sync_logs', function (Blueprint $table) {
            if (Schema::hasColumn('sync_logs', 'context')) {
                $table->dropColumn('context');
            }
            if (Schema::hasColumn('sync_logs', 'message')) {
                $table->dropColumn('message');
            }
            if (Schema::hasColumn('sync_logs', 'source')) {
                $table->dropColumn('source');
            }
            if (Schema::hasColumn('sync_logs', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
