<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('invoices', 'drive_file_name')) {
                $table->string('drive_file_name')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'drive_file_url')) {
                $table->string('drive_file_url')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'supplier')) {
                $table->string('supplier')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'invoice_number')) {
                $table->string('invoice_number')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'invoice_date')) {
                $table->date('invoice_date')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'due_date')) {
                $table->date('due_date')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency')->nullable();
            }

            if (!Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('invoices', 'net_amount')) {
                $table->decimal('net_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('invoices', 'vat_amount')) {
                $table->decimal('vat_amount', 12, 2)->nullable();
            }

            if (!Schema::hasColumn('invoices', 'raw_json')) {
                $table->longText('raw_json')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'drive_file_name',
                'drive_file_url',
                'supplier',
                'invoice_number',
                'invoice_date',
                'due_date',
                'currency',
                'total_amount',
                'net_amount',
                'vat_amount',
                'raw_json',
            ]);
        });
    }
};
