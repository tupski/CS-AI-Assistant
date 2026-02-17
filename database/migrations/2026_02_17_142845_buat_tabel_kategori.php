<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel kategori
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // nama kategori (pembayaran, pengiriman, produk, dll)
            $table->string('slug')->unique(); // slug untuk URL
            $table->string('warna', 20)->default('blue'); // warna badge (blue, green, red, yellow, purple)
            $table->string('icon', 50)->nullable(); // icon emoji atau class
            $table->text('deskripsi')->nullable(); // deskripsi kategori
            $table->boolean('aktif')->default(true); // status aktif/nonaktif
            $table->integer('urutan')->default(0); // urutan tampilan
            $table->timestamps();
        });

        // Update tabel faq untuk relasi ke kategori
        Schema::table('faq', function (Blueprint $table) {
            $table->foreignId('kategori_id')->nullable()->after('id')->constrained('kategori')->onDelete('set null');
            // Tetap simpan kategori string untuk backward compatibility
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });

        Schema::dropIfExists('kategori');
    }
};
