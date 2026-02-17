<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel user_pengaturan
     */
    public function up(): void
    {
        Schema::create('user_pengaturan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('kunci'); // groq_api_key, groq_model, ai_guidelines, dll
            $table->text('nilai')->nullable(); // Value dari setting
            $table->string('tipe')->default('text'); // text, password, textarea
            $table->timestamps();

            // Unique constraint: satu user hanya punya satu value per key
            $table->unique(['user_id', 'kunci']);

            // Index untuk performa
            $table->index('user_id');
            $table->index('kunci');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('user_pengaturan');
    }
};
