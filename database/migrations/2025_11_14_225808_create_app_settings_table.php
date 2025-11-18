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
  Schema::create('app_settings', function (Blueprint $table) {
    $table->id();
    $table->decimal('monthly_salary', 10, 2)->default(0);
    $table->decimal('per_diem', 10, 2)->default(0);
    $table->decimal('hotel_night', 10, 2)->default(0);
    $table->decimal('transport_per_km', 10, 2)->default(0);
    $table->string('drive_projects_folder_id')->nullable();
    $table->string('doc_ai_processor_id')->nullable(); // <-- important
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
