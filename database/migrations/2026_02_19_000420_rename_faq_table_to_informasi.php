<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rename tabel faq menjadi informasi
     */
    public function up(): void
    {
        Schema::rename('faq', 'informasi');
    }

    /**
     * Rollback: rename kembali ke faq
     */
    public function down(): void
    {
        Schema::rename('informasi', 'faq');
    }
};
