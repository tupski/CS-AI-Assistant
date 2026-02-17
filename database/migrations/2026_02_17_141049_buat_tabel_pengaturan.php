<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel pengaturan
     */
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('kunci')->unique(); // groq_api_key, groq_model, dll
            $table->text('nilai')->nullable(); // value dari setting
            $table->string('tipe')->default('text'); // text, password, number, boolean
            $table->string('grup')->default('umum'); // umum, api, sistem
            $table->string('label'); // Label yang ditampilkan di UI
            $table->text('deskripsi')->nullable(); // Penjelasan setting
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
