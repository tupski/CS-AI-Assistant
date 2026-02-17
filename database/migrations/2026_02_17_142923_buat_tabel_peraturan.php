<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel peraturan
     */
    public function up(): void
    {
        Schema::create('peraturan', function (Blueprint $table) {
            $table->id();
            $table->string('judul'); // judul peraturan
            $table->text('isi'); // isi peraturan/guideline
            $table->string('tipe', 50)->default('umum'); // umum, larangan, wajib, tips
            $table->string('prioritas', 20)->default('normal'); // tinggi, normal, rendah
            $table->boolean('aktif')->default(true); // status aktif/nonaktif
            $table->integer('urutan')->default(0); // urutan tampilan
            $table->timestamps();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('peraturan');
    }
};
