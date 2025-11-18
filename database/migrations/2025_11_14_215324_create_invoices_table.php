<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();

            // Relații
            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            // Meta date factură (din Document AI / manual)
            $table->string('supplier')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();

            // Bani
            $table->string('currency', 8)->default('CHF');
            $table->decimal('amount', 12, 2)->default(0);         // suma totală
            $table->decimal('net_amount', 12, 2)->nullable();
            $table->decimal('vat_amount', 12, 2)->nullable();

            // Clasificare internă
            $table->string('category', 64)->default('misc');
            $table->string('status', 32)->default('imported');    // <– AICI rezolvăm eroarea cu "Data truncated"

            // Fișier din Drive
            $table->string('drive_file_id')->unique();
            $table->string('file_name')->nullable();
            $table->string('drive_file_url')->nullable();

            // JSON brut din Document AI (pentru debugging / re-parsare)
            $table->longText('raw_json')->nullable();

            $table->timestamps();

            // Indexuri utile
            $table->index('invoice_number');
            $table->index('invoice_date');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
