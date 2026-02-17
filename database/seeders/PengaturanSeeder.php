<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Seed data pengaturan default
     */
    public function run(): void
    {
        $pengaturan = [
            [
                'kunci' => 'groq_api_key',
                'nilai' => env('GROQ_API_KEY', ''),
                'tipe' => 'password',
                'grup' => 'api',
                'label' => 'Groq API Key',
                'deskripsi' => 'API key untuk mengakses Groq AI',
            ],
            [
                'kunci' => 'groq_model',
                'nilai' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
                'tipe' => 'text',
                'grup' => 'api',
                'label' => 'Groq Model',
                'deskripsi' => 'Model AI yang digunakan',
            ],
        ];

        foreach ($pengaturan as $setting) {
            Pengaturan::firstOrCreate(
                ['kunci' => $setting['kunci']],
                $setting
            );
        }
    }
}
