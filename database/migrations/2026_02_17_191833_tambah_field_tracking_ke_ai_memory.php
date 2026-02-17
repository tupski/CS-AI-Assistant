<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tambah field tracking
     */
    public function up(): void
    {
        Schema::table('ai_memory', function (Blueprint $table) {
            $table->string('jawaban_disalin')->nullable()->after('jawaban_singkat'); // formal, santai, singkat, atau null
            $table->timestamp('disalin_pada')->nullable()->after('jawaban_disalin'); // Kapan jawaban disalin
            $table->integer('copy_count')->default(0)->after('disalin_pada'); // Berapa kali jawaban ini disalin

            // Index untuk query
            $table->index('jawaban_disalin');
            $table->index('copy_count');
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('ai_memory', function (Blueprint $table) {
            $table->dropIndex(['jawaban_disalin']);
            $table->dropIndex(['copy_count']);
            $table->dropColumn(['jawaban_disalin', 'disalin_pada', 'copy_count']);
        });
    }
};
