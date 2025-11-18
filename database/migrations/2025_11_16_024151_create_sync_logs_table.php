<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('sync_logs', function (Blueprint $table) {
        $table->id();
        $table->string('status')->default('info'); // success / error / info
        $table->string('source')->nullable();      // ex: drive_sync
        $table->text('message');                   // mesaj scurt de afișat în UI
        $table->json('context')->nullable();       // detalii tehnice, dacă vrei
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
