<?php

namespace Database\Seeders;

use App\Models\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    /**
     * Seed default AI providers
     */
    public function run(): void
    {
        $providers = [
            [
                'nama' => 'Groq - Llama 3.3 70B',
                'tipe' => 'groq',
                'model' => 'llama-3.3-70b-versatile',
                'api_url' => 'https://api.groq.com/openai/v1/chat/completions',
                'aktif' => true,
                'prioritas' => 0,
                'quota_limit' => null, // Unlimited
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
            [
                'nama' => 'Groq - Llama 3.1 8B',
                'tipe' => 'groq',
                'model' => 'llama-3.1-8b-instant',
                'api_url' => 'https://api.groq.com/openai/v1/chat/completions',
                'aktif' => false,
                'prioritas' => 1,
                'quota_limit' => null,
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
            [
                'nama' => 'Google Gemini 2.0 Flash',
                'tipe' => 'gemini',
                'model' => 'gemini-2.0-flash-exp',
                'api_url' => null, // Will use default
                'aktif' => false,
                'prioritas' => 2,
                'quota_limit' => 1500, // Free tier limit per day
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
            [
                'nama' => 'OpenAI GPT-4o',
                'tipe' => 'openai',
                'model' => 'gpt-4o',
                'api_url' => 'https://api.openai.com/v1/chat/completions',
                'aktif' => false,
                'prioritas' => 3,
                'quota_limit' => null,
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
            [
                'nama' => 'OpenAI GPT-4o Mini',
                'tipe' => 'openai',
                'model' => 'gpt-4o-mini',
                'api_url' => 'https://api.openai.com/v1/chat/completions',
                'aktif' => false,
                'prioritas' => 4,
                'quota_limit' => null,
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
            [
                'nama' => 'Anthropic Claude 3.5 Sonnet',
                'tipe' => 'anthropic',
                'model' => 'claude-3-5-sonnet-20241022',
                'api_url' => 'https://api.anthropic.com/v1/messages',
                'aktif' => false,
                'prioritas' => 5,
                'quota_limit' => null,
                'config' => [
                    'temperature' => 0.7,
                    'max_tokens' => 2000,
                ],
            ],
        ];

        foreach ($providers as $provider) {
            AiProvider::updateOrCreate(
                [
                    'tipe' => $provider['tipe'],
                    'model' => $provider['model'],
                    'user_id' => null, // Global provider
                ],
                $provider
            );
        }

        $this->command->info('✅ AI Providers seeded successfully!');
        $this->command->info('💡 Jangan lupa set API key di halaman pengaturan AI Provider');
    }
}
