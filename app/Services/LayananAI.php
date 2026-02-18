<?php

namespace App\Services;

use App\Models\AiMemory;
use App\Models\AiProvider;
use App\Models\Faq;
use App\Models\LogChat;
use App\Models\Pengaturan;
use App\Models\Peraturan;
use App\Models\UserPengaturan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananAI
{
    protected ?int $userId;
    protected ?AiProvider $provider;
    protected bool $autoRotate;

    public function __construct(?int $userId = null, ?int $providerId = null, bool $autoRotate = true)
    {
        $this->userId = $userId;
        $this->autoRotate = $autoRotate;

        // Jika providerId diberikan, gunakan provider tersebut
        if ($providerId) {
            $this->provider = AiProvider::find($providerId);
        } else {
            // Ambil provider terbaik (dengan quota dan prioritas)
            $this->provider = AiProvider::getBestProvider($userId);
        }

        if (!$this->provider) {
            throw new \Exception('Tidak ada AI provider yang tersedia. Silakan tambahkan API key terlebih dahulu.');
        }
    }

    /**
     * Generate 3 versi jawaban dari chat member
     */
    public function generateJawaban(string $pesanMember): array
    {
        $systemPrompt = $this->buatSystemPrompt();

        $userPrompt = <<<PROMPT
Chat Member: "{$pesanMember}"

Analisis chat di atas dan berikan 3 versi jawaban dalam format JSON.
PROMPT;

        try {
            $response = $this->callAI($systemPrompt, $userPrompt);

            // Increment quota usage
            $this->provider->incrementQuota();
            $this->provider->resetError();

            // Cari FAQ yang relevan
            $response['faq_relevan'] = $this->cariFaqRelevan($pesanMember, $response['kategori'] ?? null);

            return $response;
        } catch (\Exception $e) {
            // Catat error
            $this->provider->catatError($e->getMessage());

            // Jika auto-rotate enabled, coba provider lain
            if ($this->autoRotate) {
                return $this->retryWithNextProvider($pesanMember, $e);
            }

            throw $e;
        }
    }

    /**
     * Cari FAQ yang relevan dengan pertanyaan member
     */
    protected function cariFaqRelevan(string $pesanMember, ?string $kategori = null): array
    {
        $query = Faq::query();

        // Filter by kategori jika ada
        if ($kategori) {
            $query->whereHas('kategori', function ($q) use ($kategori) {
                $q->where('nama', 'like', "%{$kategori}%");
            });
        }

        // Cari FAQ yang judulnya atau isinya mirip dengan pesan member
        $keywords = $this->extractKeywords($pesanMember);

        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $q->orWhere('judul', 'like', "%{$keyword}%")
                      ->orWhere('isi', 'like', "%{$keyword}%");
                }
            });
        }

        return $query->with('kategori')
            ->limit(3)
            ->get()
            ->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'pertanyaan' => $faq->judul,
                    'jawaban' => $faq->isi,
                    'kategori' => $faq->kategori->nama ?? 'Umum',
                ];
            })
            ->toArray();
    }

    /**
     * Extract keywords dari pesan untuk search
     */
    protected function extractKeywords(string $text): array
    {
        // Lowercase dan hapus karakter khusus
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);

        // Split jadi kata-kata
        $words = explode(' ', $text);

        // Filter kata-kata pendek dan stopwords
        $stopwords = ['yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'pada', 'dengan', 'adalah', 'ini', 'itu', 'saya', 'anda', 'kamu', 'nya', 'ya', 'tidak', 'bisa', 'mau', 'dong', 'min', 'kak', 'bang'];

        $keywords = array_filter($words, function ($word) use ($stopwords) {
            return strlen($word) > 3 && !in_array($word, $stopwords);
        });

        return array_unique(array_values($keywords));
    }

    /**
     * Retry dengan provider berikutnya
     */
    protected function retryWithNextProvider(string $pesanMember, \Exception $previousError): array
    {
        // Ambil provider berikutnya
        $nextProvider = AiProvider::getBestProvider($this->userId);

        if (!$nextProvider || $nextProvider->id === $this->provider->id) {
            // Tidak ada provider lain, throw error asli
            throw new \Exception(
                "Semua AI provider gagal atau habis quota. Error terakhir: " . $previousError->getMessage()
            );
        }

        Log::info("Auto-rotating dari {$this->provider->nama} ke {$nextProvider->nama}");

        // Switch provider dan retry
        $this->provider = $nextProvider;
        return $this->generateJawaban($pesanMember);
    }

    /**
     * Call AI berdasarkan tipe provider
     */
    protected function callAI(string $systemPrompt, string $userPrompt): array
    {
        switch ($this->provider->tipe) {
            case 'groq':
                return $this->callGroq($systemPrompt, $userPrompt);
            case 'openai':
                return $this->callOpenAI($systemPrompt, $userPrompt);
            case 'gemini':
                return $this->callGemini($systemPrompt, $userPrompt);
            case 'anthropic':
                return $this->callAnthropic($systemPrompt, $userPrompt);
            default:
                throw new \Exception("Provider tipe '{$this->provider->tipe}' belum didukung");
        }
    }

    /**
     * Call Groq API
     */
    protected function callGroq(string $systemPrompt, string $userPrompt): array
    {
        $apiUrl = $this->provider->api_url ?? 'https://api.groq.com/openai/v1/chat/completions';
        $config = $this->provider->config ?? [];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->provider->api_key,
            'Content-Type' => 'application/json',
        ])
        ->withOptions([
            'verify' => config('app.env') === 'production',
        ])
        ->timeout(60)
        ->post($apiUrl, [
            'model' => $this->provider->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 2000,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            throw new \Exception("Groq API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';

        return $this->parseResponse($content);
    }

    /**
     * Call OpenAI API (GPT-4, GPT-4o, dll)
     */
    protected function callOpenAI(string $systemPrompt, string $userPrompt): array
    {
        $apiUrl = $this->provider->api_url ?? 'https://api.openai.com/v1/chat/completions';
        $config = $this->provider->config ?? [];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->provider->api_key,
            'Content-Type' => 'application/json',
        ])
        ->timeout(60)
        ->post($apiUrl, [
            'model' => $this->provider->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 2000,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            throw new \Exception("OpenAI API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? '';

        return $this->parseResponse($content);
    }

    /**
     * Call Google Gemini API
     */
    protected function callGemini(string $systemPrompt, string $userPrompt): array
    {
        $apiUrl = $this->provider->api_url ??
            "https://generativelanguage.googleapis.com/v1beta/models/{$this->provider->model}:generateContent";
        $config = $this->provider->config ?? [];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->timeout(60)
        ->post($apiUrl . '?key=' . $this->provider->api_key, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $systemPrompt . "\n\n" . $userPrompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $config['temperature'] ?? 0.7,
                'maxOutputTokens' => $config['max_tokens'] ?? 2000,
                'responseMimeType' => 'application/json',
            ],
        ]);

        if (!$response->successful()) {
            throw new \Exception("Gemini API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        return $this->parseResponse($content);
    }

    /**
     * Call Anthropic Claude API
     */
    protected function callAnthropic(string $systemPrompt, string $userPrompt): array
    {
        $apiUrl = $this->provider->api_url ?? 'https://api.anthropic.com/v1/messages';
        $config = $this->provider->config ?? [];

        $response = Http::withHeaders([
            'x-api-key' => $this->provider->api_key,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])
        ->timeout(60)
        ->post($apiUrl, [
            'model' => $this->provider->model,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => $config['temperature'] ?? 0.7,
            'max_tokens' => $config['max_tokens'] ?? 2000,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Anthropic API Error: " . $response->body());
        }

        $data = $response->json();
        $content = $data['content'][0]['text'] ?? '';

        return $this->parseResponse($content);
    }

    /**
     * Parse response JSON dari AI
     */
    protected function parseResponse(string $content): array
    {
        $decoded = json_decode($content, true);

        if (!$decoded) {
            throw new \Exception("Gagal parse response JSON dari AI");
        }

        return [
            'kategori' => $decoded['kategori'] ?? 'umum',
            'formal' => $decoded['formal'] ?? '',
            'santai' => $decoded['santai'] ?? '',
            'singkat' => $decoded['singkat'] ?? '',
        ];
    }

    /**
     * Buat system prompt khusus untuk CS
     */
    protected function buatSystemPrompt(): string
    {
        // Ambil peraturan aktif dari database
        $peraturans = Peraturan::aktif()->urutan()->get();
        $peraturanText = $this->formatPeraturan($peraturans);

        // Ambil FAQ yang relevan
        $faqs = Faq::with('kategori')->limit(10)->get();
        $faqText = $this->formatFaq($faqs);

        // Ambil AI Memory user sendiri (prioritas tinggi - yang pernah disalin)
        $userMemories = collect();
        if ($this->userId) {
            $userMemories = AiMemory::byUser($this->userId)
                ->whereNotNull('jawaban_disalin')
                ->orderBy('copy_count', 'desc')
                ->limit(3)
                ->get();
        }
        $userMemoryText = $this->formatMemory($userMemories, 'User');

        // Ambil AI Memory global (contoh jawaban terbaik dari semua user)
        $globalMemories = AiMemory::goodExamples()
            ->mostCopied(3)
            ->get();
        $globalMemoryText = $this->formatMemory($globalMemories, 'Global');

        // Ambil contoh chat dari log user sendiri (5 terakhir)
        $contohChatQuery = LogChat::with('user')->latest()->limit(5);
        if ($this->userId) {
            $contohChatQuery->where('user_id', $this->userId);
        }
        $contohChat = $contohChatQuery->get();
        $contohChatText = $this->formatContohChat($contohChat);

        // Ambil AI guidelines - user settings prioritas, fallback ke global
        $aiGuidelines = '';
        if ($this->userId) {
            $aiGuidelines = UserPengaturan::ambil($this->userId, 'ai_guidelines')
                ?? Pengaturan::ambil('ai_guidelines', '');
        } else {
            $aiGuidelines = Pengaturan::ambil('ai_guidelines', '');
        }
        $guidelinesText = $aiGuidelines ? "\n\nPANDUAN TAMBAHAN:\n{$aiGuidelines}\n" : '';

        return <<<PROMPT
Kamu adalah asisten AI untuk Customer Service yang profesional dan membantu.

ATURAN PENTING:
1. Tidak boleh berdebat dengan member, selalu sopan dan empati
2. Tidak boleh membuat janji di luar kebijakan perusahaan
3. Harus sopan dalam segala kondisi
4. Jika nada member terlihat marah atau kecewa, tingkatkan empati dalam jawaban
5. Jawaban harus berdasarkan konteks yang diberikan
6. Jangan membuat asumsi tentang kebijakan yang tidak disebutkan
{$guidelinesText}
PERATURAN & GUIDELINES CS:
{$peraturanText}

FAQ KNOWLEDGE BASE:
{$faqText}

CONTOH JAWABAN TERBAIK ANDA (Memory Pribadi - pelajari dari jawaban yang pernah Anda salin):
{$userMemoryText}

CONTOH JAWABAN TERBAIK GLOBAL (Memory dari semua CS - pelajari pola terbaik):
{$globalMemoryText}

CONTOH CHAT SEBELUMNYA (untuk referensi gaya bahasa):
{$contohChatText}

TUGAS:
Berikan 3 versi jawaban untuk chat member:
1. FORMAL - Bahasa formal dan profesional
2. SANTAI - Bahasa lebih santai tapi tetap sopan
3. SINGKAT - Jawaban singkat dan to the point

OUTPUT FORMAT (WAJIB JSON):
{
    "kategori": "kategori pertanyaan (misal: pembayaran, bonus, teknis, dll)",
    "formal": "jawaban versi formal",
    "santai": "jawaban versi santai",
    "singkat": "jawaban versi singkat"
}
PROMPT;
    }

    /**
     * Format peraturan untuk system prompt
     */
    protected function formatPeraturan($peraturans): string
    {
        if ($peraturans->isEmpty()) {
            return "Tidak ada peraturan khusus.";
        }

        $grouped = $peraturans->groupBy('tipe');
        $text = "";

        $tipeLabels = [
            'wajib' => '✅ WAJIB DILAKUKAN',
            'larangan' => '🚫 LARANGAN',
            'tips' => '💡 TIPS & TRIK',
            'umum' => '📋 PERATURAN UMUM',
        ];

        foreach ($grouped as $tipe => $items) {
            $label = $tipeLabels[$tipe] ?? strtoupper($tipe);
            $text .= "\n{$label}:\n";

            foreach ($items as $peraturan) {
                $prioritas = $peraturan->prioritas === 'tinggi' ? ' [PRIORITAS TINGGI]' : '';
                $text .= "- {$peraturan->judul}{$prioritas}\n  {$peraturan->isi}\n";
            }
        }

        return $text;
    }

    /**
     * Format FAQ untuk system prompt
     */
    protected function formatFaq($faqs): string
    {
        if ($faqs->isEmpty()) {
            return "Belum ada FAQ tersedia.";
        }

        $text = "";
        foreach ($faqs as $index => $faq) {
            $num = $index + 1;
            $kategori = '';
            if ($faq->kategori && is_object($faq->kategori)) {
                $kategori = " [{$faq->kategori->nama}]";
            }
            $text .= "\nFAQ {$num}{$kategori}:\n";
            $text .= "Q: {$faq->judul}\n";
            $text .= "A: " . substr($faq->isi, 0, 300) . "\n";
        }

        return $text;
    }

    /**
     * Format AI Memory untuk system prompt
     */
    protected function formatMemory($memories, string $label = 'Contoh'): string
    {
        if ($memories->isEmpty()) {
            return "Belum ada contoh jawaban tersimpan.";
        }

        $text = "";
        foreach ($memories as $index => $memory) {
            $num = $index + 1;
            $copyInfo = $memory->jawaban_disalin ? " [Disalin: {$memory->jawaban_disalin}, {$memory->copy_count}x]" : "";
            $text .= "\n{$label} {$num} [{$memory->kategori_terdeteksi}]{$copyInfo}:\n";
            $text .= "Member: " . substr($memory->pesan_member, 0, 150) . "\n";

            // Tampilkan jawaban yang disalin sebagai prioritas
            if ($memory->jawaban_disalin) {
                $jawabanKey = 'jawaban_' . $memory->jawaban_disalin;
                $text .= "CS ({$memory->jawaban_disalin}): " . substr($memory->$jawabanKey, 0, 250) . "\n";
            } else {
                $text .= "CS (Santai): " . substr($memory->jawaban_santai, 0, 200) . "\n";
                $text .= "CS (Formal): " . substr($memory->jawaban_formal, 0, 200) . "\n";
            }

            // Increment usage count
            $memory->incrementUsage();
        }

        return $text;
    }

    /**
     * Format contoh chat untuk system prompt
     */
    protected function formatContohChat($logChats): string
    {
        if ($logChats->isEmpty()) {
            return "Belum ada contoh chat sebelumnya.";
        }

        $text = "";
        foreach ($logChats as $index => $log) {
            $num = $index + 1;
            $text .= "\nContoh {$num}:\n";
            $text .= "Member: " . substr($log->pesan_member, 0, 200) . "\n";
            $text .= "CS (Santai): " . substr($log->jawaban_santai, 0, 200) . "\n";
        }

        return $text;
    }

    /**
     * Simpan hasil generate ke AI Memory untuk learning
     */
    public function saveToMemory(string $pesanMember, array $hasil, int $userId, bool $isGoodExample = false): AiMemory
    {
        // Snapshot peraturan dan FAQ saat ini
        $peraturans = Peraturan::aktif()->urutan()->get();
        $faqs = Faq::limit(10)->get();

        $memory = AiMemory::create([
            'user_id' => $userId,
            'pesan_member' => $pesanMember,
            'kategori_terdeteksi' => $hasil['kategori'],
            'jawaban_formal' => $hasil['formal'],
            'jawaban_santai' => $hasil['santai'],
            'jawaban_singkat' => $hasil['singkat'],
            'is_good_example' => $isGoodExample,
            'snapshot_peraturan' => $peraturans->toJson(),
            'snapshot_faq' => $faqs->toJson(),
            'system_prompt_used' => $this->buatSystemPrompt(),
        ]);

        return $memory;
    }

    /**
     * Get provider yang sedang digunakan
     */
    public function getProvider(): ?AiProvider
    {
        return $this->provider;
    }
}
