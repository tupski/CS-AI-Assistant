<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data awal
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PengaturanSeeder::class,
            UserSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
