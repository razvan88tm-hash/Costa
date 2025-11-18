<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_worker_logs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('worker_name')->nullable();

            $table->integer('days_on_site')->default(0);
            $table->decimal('hours_on_site', 8, 2)->default(0);
            $table->decimal('transport_km', 8, 2)->default(0);

            // costuri calculate
            $table->decimal('per_diem_total', 12, 2)->default(0);
            $table->decimal('hotel_total', 12, 2)->default(0);
            $table->decimal('labor_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('worker_name');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_worker_logs');
    }
};
