<?php

namespace App\Http\Controllers;

use App\Models\LogChat;
use App\Services\LayananGroq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected LayananGroq $layananGroq;

    public function __construct(LayananGroq $layananGroq)
    {
        $this->layananGroq = $layananGroq;
    }

    /**
     * Tampilkan halaman dashboard
     */
    public function index()
    {
        return view('dashboard');
    }

    /**
     * Generate jawaban dari AI
     */
    public function generateJawaban(Request $request)
    {
        // Validasi input
        $request->validate([
            'pesan_member' => 'required|string|min:5',
        ], [
            'pesan_member.required' => 'Pesan member wajib diisi',
            'pesan_member.min' => 'Pesan member minimal 5 karakter',
        ]);

        try {
            $pesanMember = $request->input('pesan_member');

            // Generate jawaban pakai AI
            $hasil = $this->layananGroq->generateJawaban($pesanMember);

            // Simpan ke log
            $log = LogChat::create([
                'pesan_member' => $pesanMember,
                'kategori_terdeteksi' => $hasil['kategori'],
                'jawaban_formal' => $hasil['formal'],
                'jawaban_santai' => $hasil['santai'],
                'jawaban_singkat' => $hasil['singkat'],
                'provider_digunakan' => 'groq',
                'user_id' => Auth::id(),
            ]);

            // Simpan ke AI Memory untuk learning
            $memory = $this->layananGroq->saveToMemory(
                $pesanMember,
                $hasil,
                Auth::id(),
                true // Default semua dianggap good example
            );

            return response()->json([
                'sukses' => true,
                'data' => [
                    'kategori' => $hasil['kategori'],
                    'formal' => $hasil['formal'],
                    'santai' => $hasil['santai'],
                    'singkat' => $hasil['singkat'],
                    'log_id' => $log->id,
                    'memory_id' => $memory->id,
                ],
                'pesan' => 'Jawaban berhasil di-generate!',
            ]);

        } catch (\Exception $e) {
            Log::error('Error generate jawaban', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal generate jawaban: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ambil history log chat
     */
    public function riwayat(Request $request)
    {
        $limit = $request->input('limit', 10);

        $logs = LogChat::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->limit($limit)
            ->get();

        return response()->json([
            'sukses' => true,
            'data' => $logs,
        ]);
    }
}
