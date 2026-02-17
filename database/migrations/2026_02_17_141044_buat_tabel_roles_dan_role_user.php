<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk tabel roles dan role_user
     */
    public function up(): void
    {
        // Tabel roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique(); // admin, supervisor, cs
            $table->string('label'); // Admin, Supervisor, Customer Service
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Tabel pivot role_user (many-to-many)
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Pastikan user tidak punya role duplikat
            $table->unique(['user_id', 'role_id']);
        });
    }

    /**
     * Rollback migration
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
