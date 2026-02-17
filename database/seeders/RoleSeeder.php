<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed data roles default
     */
    public function run(): void
    {
        $roles = [
            [
                'nama' => 'admin',
                'label' => 'Admin',
                'deskripsi' => 'Administrator dengan akses penuh ke semua fitur',
            ],
            [
                'nama' => 'supervisor',
                'label' => 'Supervisor',
                'deskripsi' => 'Supervisor yang bisa melihat laporan dan monitoring',
            ],
            [
                'nama' => 'cs',
                'label' => 'Customer Service',
                'deskripsi' => 'Customer Service yang menggunakan AI assistant',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['nama' => $role['nama']],
                $role
            );
        }
    }
}
