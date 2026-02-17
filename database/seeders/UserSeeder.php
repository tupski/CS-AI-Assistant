<?php

namespace Database\Seeders;

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
        // Cek dulu, kalau udah ada jangan bikin lagi
        if (User::where('email', 'cs@example.com')->exists()) {
            $this->command->info('User CS sudah ada, skip...');
            return;
        }

        User::create([
            'name' => 'CS Admin',
            'email' => 'cs@example.com',
            'password' => Hash::make('password123'), // Ganti password ini di production!
        ]);

        $this->command->info('User CS berhasil dibuat!');
        $this->command->info('Email: cs@example.com');
        $this->command->info('Password: password123');
    }
}
