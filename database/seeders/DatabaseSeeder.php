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
            UserSeeder::class,
        ]);
    }
}
