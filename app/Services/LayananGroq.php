<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananGroq
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->apiUrl = config('services.groq.api_url');
        $this->model = config('services.groq.model');
    }

    /**
     * Generate 3 versi jawaban dari chat member
     * 
     * @param string $pesanMember Chat dari member yang mau dijawab
     * @return array ['kategori' => '...', 'formal' => '...', 'santai' => '...', 'singkat' => '...']
     */
    public function generateJawaban(string $pesanMember): array
    {
        try {
            $systemPrompt = $this->buatSystemPrompt();
            $userPrompt = $this->buatUserPrompt($pesanMember);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userPrompt,
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1000,
            ]);

            if (!$response->successful()) {
                Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Gagal menghubungi Groq API: ' . $response->status());
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            return $this->parseResponse($content);

        } catch (\Exception $e) {
            Log::error('Error generate jawaban', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Buat system prompt khusus untuk CS
     */
    protected function buatSystemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah asisten AI untuk Customer Service yang profesional dan membantu.

ATURAN PENTING:
1. Tidak boleh berdebat dengan member, selalu sopan dan empati
2. Tidak boleh membuat janji di luar kebijakan perusahaan
3. Harus sopan dalam segala kondisi
4. Jika nada member terlihat marah atau kecewa, tingkatkan empati dalam jawaban
5. Jawaban harus berdasarkan konteks yang diberikan
6. Jangan membuat asumsi tentang kebijakan yang tidak disebutkan

TUGAS:
Berikan 3 versi jawaban untuk chat member:
1. FORMAL - Bahasa formal dan profesional
2. SANTAI - Bahasa lebih santai tapi tetap sopan
3. SINGKAT - Jawaban singkat dan to the point

OUTPUT FORMAT (WAJIB JSON):
{
  "kategori": "kategori pertanyaan (misal: pembayaran, pengiriman, komplain, dll)",
  "formal": "jawaban versi formal",
  "santai": "jawaban versi santai",
  "singkat": "jawaban versi singkat"
}

PENTING: Response harus valid JSON, jangan tambahkan teks apapun di luar JSON.
PROMPT;
    }

    /**
     * Buat user prompt dengan pesan member
     */
    protected function buatUserPrompt(string $pesanMember): string
    {
        return "Chat dari member:\n\n{$pesanMember}\n\nBerikan 3 versi jawaban dalam format JSON seperti yang diminta.";
    }

    /**
     * Parse response dari AI jadi array
     */
    protected function parseResponse(string $content): array
    {
        // Coba parse JSON
        $content = trim($content);
        
        // Hapus markdown code block kalau ada
        $content = preg_replace('/```json\s*/', '', $content);
        $content = preg_replace('/```\s*/', '', $content);
        $content = trim($content);

        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Response AI bukan JSON valid: ' . json_last_error_msg());
        }

        // Validasi struktur
        $required = ['kategori', 'formal', 'santai', 'singkat'];
        foreach ($required as $field) {
            if (!isset($decoded[$field])) {
                throw new \Exception("Field '{$field}' tidak ada dalam response AI");
            }
        }

        return $decoded;
    }
}

