<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UpdateExistingUserSeeder extends Seeder
{
    /**
     * Update user yang sudah ada dengan role admin
     */
    public function run(): void
    {
        // Cari user dengan email cs@example.com
        $user = User::where('email', 'cs@example.com')->first();

        if ($user) {
            // Ambil role admin
            $adminRole = Role::where('nama', 'admin')->first();

            if ($adminRole) {
                // Attach role admin jika belum ada
                if (!$user->roles()->where('role_id', $adminRole->id)->exists()) {
                    $user->roles()->attach($adminRole->id);
                    $this->command->info("✅ User {$user->email} berhasil ditambahkan role admin");
                } else {
                    $this->command->info("ℹ️ User {$user->email} sudah memiliki role admin");
                }
            } else {
                $this->command->error("❌ Role admin tidak ditemukan");
            }
        } else {
            $this->command->warn("⚠️ User dengan email cs@example.com tidak ditemukan");
        }
    }
}

