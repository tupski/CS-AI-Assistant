<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk buat tabel faq
     */
    public function up(): void
    {
        Schema::create('faq', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 100)->index(); // kategori pertanyaan (misal: pembayaran, pengiriman, dll)
            $table->string('judul'); // judul singkat FAQ
            $table->text('isi'); // isi jawaban lengkap
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('faq');
    }
};
