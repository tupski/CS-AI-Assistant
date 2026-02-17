<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk buat tabel log_chat
     */
    public function up(): void
    {
        Schema::create('log_chat', function (Blueprint $table) {
            $table->id();
            $table->text('pesan_member'); // chat dari member yang di-paste
            $table->string('kategori_terdeteksi', 100)->nullable(); // kategori yang terdeteksi AI
            $table->text('jawaban_formal'); // versi jawaban formal
            $table->text('jawaban_santai'); // versi jawaban santai
            $table->text('jawaban_singkat'); // versi jawaban singkat
            $table->string('provider_digunakan', 50)->default('groq'); // AI provider yang dipakai
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // CS yang generate
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('log_chat');
    }
};
