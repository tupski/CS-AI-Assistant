<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel ai_provider
     */
    public function up(): void
    {
        Schema::create('ai_provider', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nama'); // Groq, Gemini, GPT-4, Claude, dll
            $table->string('tipe'); // groq, gemini, openai, anthropic
            $table->string('model'); // llama-3.3-70b, gemini-2.0-flash, gpt-4o, claude-3.5-sonnet
            $table->text('api_key')->nullable(); // Encrypted API key
            $table->string('api_url')->nullable(); // Custom API URL jika perlu
            $table->boolean('aktif')->default(true); // Enable/disable provider
            $table->integer('prioritas')->default(0); // Urutan rotasi (0 = tertinggi)
            $table->integer('quota_limit')->nullable(); // Limit request per hari (null = unlimited)
            $table->integer('quota_used')->default(0); // Jumlah request hari ini
            $table->date('quota_reset_date')->nullable(); // Tanggal reset quota
            $table->integer('error_count')->default(0); // Counter error berturut-turut
            $table->timestamp('last_used_at')->nullable(); // Terakhir digunakan
            $table->timestamp('last_error_at')->nullable(); // Terakhir error
            $table->text('last_error_message')->nullable(); // Pesan error terakhir
            $table->json('config')->nullable(); // Config tambahan (temperature, max_tokens, dll)
            $table->timestamps();

            // Index untuk performa
            $table->index('user_id');
            $table->index('tipe');
            $table->index(['aktif', 'prioritas']);
            $table->index('quota_reset_date');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_provider');
    }
};
