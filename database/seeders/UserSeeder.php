<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Bikin user default untuk testing
     */
    public function run(): void
    {
        // User Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
            ]
        );

        $roleAdmin = Role::where('nama', 'admin')->first();
        if ($roleAdmin && !$admin->roles->contains($roleAdmin->id)) {
            $admin->roles()->attach($roleAdmin->id);
            $this->command->info('User Admin berhasil dibuat!');
            $this->command->info('Email: admin@example.com');
            $this->command->info('Password: admin123');
        }

        // User CS
        $cs = User::firstOrCreate(
            ['email' => 'cs@example.com'],
            [
                'name' => 'CS Staff',
                'password' => Hash::make('password123'),
            ]
        );

        $roleCs = Role::where('nama', 'cs')->first();
        if ($roleCs && !$cs->roles->contains($roleCs->id)) {
            $cs->roles()->attach($roleCs->id);
            $this->command->info('User CS berhasil dibuat!');
            $this->command->info('Email: cs@example.com');
            $this->command->info('Password: password123');
        }
    }
}
