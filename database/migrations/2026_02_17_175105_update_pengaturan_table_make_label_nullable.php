<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk update tabel pengaturan
     */
    public function up(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->string('label')->nullable()->change();
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->string('label')->nullable(false)->change();
        });
    }
};
