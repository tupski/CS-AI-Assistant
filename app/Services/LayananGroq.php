<?php

namespace App\Services;

use App\Models\AiMemory;
use App\Models\Faq;
use App\Models\LogChat;
use App\Models\Pengaturan;
use App\Models\Peraturan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananGroq
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $model;

    public function __construct()
    {
        // Ambil dari database dulu, fallback ke config
        $this->apiKey = Pengaturan::ambil('groq_api_key', config('services.groq.api_key'));
        $this->apiUrl = config('services.groq.api_url');
        $this->model = Pengaturan::ambil('groq_model', config('services.groq.model'));
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
            ])
            ->withOptions([
                'verify' => config('app.env') === 'production', // Disable SSL verify di development
            ])
            ->timeout(30)
            ->post($this->apiUrl, [
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
        // Ambil peraturan aktif dari database
        $peraturans = Peraturan::aktif()->urutan()->get();
        $peraturanText = $this->formatPeraturan($peraturans);

        // Ambil FAQ yang relevan
        $faqs = Faq::with('kategori')->limit(10)->get();
        $faqText = $this->formatFaq($faqs);

        // Ambil AI Memory (contoh jawaban terbaik)
        $memories = AiMemory::goodExamples()
            ->latest()
            ->limit(5)
            ->get();
        $memoryText = $this->formatMemory($memories);

        // Ambil contoh chat dari log (5 terakhir)
        $contohChat = LogChat::with('user')
            ->latest()
            ->limit(5)
            ->get();
        $contohChatText = $this->formatContohChat($contohChat);

        // Ambil AI guidelines dari pengaturan (jika ada)
        $aiGuidelines = Pengaturan::ambil('ai_guidelines', '');
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

CONTOH JAWABAN TERBAIK (AI Memory - pelajari pola dan gaya bahasa):
{$memoryText}

CONTOH CHAT SEBELUMNYA (untuk referensi gaya bahasa):
{$contohChatText}

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
    protected function formatMemory($memories): string
    {
        if ($memories->isEmpty()) {
            return "Belum ada contoh jawaban tersimpan.";
        }

        $text = "";
        foreach ($memories as $index => $memory) {
            $num = $index + 1;
            $text .= "\nContoh Terbaik {$num} [{$memory->kategori_terdeteksi}]:\n";
            $text .= "Member: " . substr($memory->pesan_member, 0, 150) . "\n";
            $text .= "CS (Santai): " . substr($memory->jawaban_santai, 0, 200) . "\n";
            $text .= "CS (Formal): " . substr($memory->jawaban_formal, 0, 200) . "\n";

            // Increment usage count
            $memory->incrementUsage();
        }

        return $text;
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

    /**
     * Simpan hasil generate ke AI Memory
     */
    public function saveToMemory(
        string $pesanMember,
        array $hasil,
        ?int $userId = null,
        bool $isGoodExample = true
    ): AiMemory {
        // Ambil peraturan yang digunakan (snapshot)
        $peraturans = Peraturan::aktif()->urutan()->get();
        $peraturanSnapshot = $peraturans->map(function ($p) {
            return [
                'judul' => $p->judul,
                'isi' => $p->isi,
                'tipe' => $p->tipe,
                'prioritas' => $p->prioritas,
            ];
        })->toArray();

        // Ambil FAQ yang relevan (snapshot)
        $faqs = Faq::with('kategori')->limit(5)->get();
        $faqSnapshot = $faqs->map(function ($f) {
            return [
                'judul' => $f->judul,
                'isi' => substr($f->isi, 0, 200),
                'kategori' => $f->kategori && is_object($f->kategori) ? $f->kategori->nama : null,
            ];
        })->toArray();

        // Simpan ke database
        return AiMemory::create([
            'pesan_member' => $pesanMember,
            'kategori_terdeteksi' => $hasil['kategori'] ?? null,
            'jawaban_formal' => $hasil['formal'],
            'jawaban_santai' => $hasil['santai'],
            'jawaban_singkat' => $hasil['singkat'],
            'system_prompt_used' => $this->buatSystemPrompt(),
            'peraturan_used' => $peraturanSnapshot,
            'faq_used' => $faqSnapshot,
            'provider_digunakan' => 'groq',
            'user_id' => $userId,
            'is_good_example' => $isGoodExample,
            'usage_count' => 0,
        ]);
    }
}

