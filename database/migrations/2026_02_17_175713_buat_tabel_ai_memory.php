<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel ai_memory
     */
    public function up(): void
    {
        Schema::create('ai_memory', function (Blueprint $table) {
            $table->id();
            $table->text('pesan_member'); // Pertanyaan dari member
            $table->string('kategori_terdeteksi')->nullable(); // Kategori yang terdeteksi AI
            $table->text('jawaban_formal'); // Jawaban versi formal
            $table->text('jawaban_santai'); // Jawaban versi santai
            $table->text('jawaban_singkat'); // Jawaban versi singkat
            $table->text('system_prompt_used')->nullable(); // System prompt yang digunakan saat generate
            $table->json('peraturan_used')->nullable(); // Peraturan yang digunakan (snapshot)
            $table->json('faq_used')->nullable(); // FAQ yang relevan (snapshot)
            $table->string('provider_digunakan')->default('groq'); // Provider AI yang digunakan
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // User yang generate
            $table->boolean('is_good_example')->default(true); // Apakah ini contoh yang bagus untuk learning
            $table->integer('usage_count')->default(0); // Berapa kali digunakan sebagai referensi
            $table->timestamps();

            // Index untuk performa
            $table->index('kategori_terdeteksi');
            $table->index('is_good_example');
            $table->index('created_at');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_memory');
    }
};
