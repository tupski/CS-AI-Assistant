<?php

namespace App\Http\Controllers;

use App\Models\AiMemory;
use App\Models\LogChat;
use App\Services\LayananGroq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
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
            $userId = Auth::id();

            // Buat instance LayananGroq dengan user context
            $layananGroq = new LayananGroq($userId);

            // Generate jawaban pakai AI
            $hasil = $layananGroq->generateJawaban($pesanMember);

            // Simpan ke log
            $log = LogChat::create([
                'pesan_member' => $pesanMember,
                'kategori_terdeteksi' => $hasil['kategori'],
                'jawaban_formal' => $hasil['formal'],
                'jawaban_santai' => $hasil['santai'],
                'jawaban_singkat' => $hasil['singkat'],
                'provider_digunakan' => 'groq',
                'user_id' => $userId,
            ]);

            // Simpan ke AI Memory untuk learning
            $memory = $layananGroq->saveToMemory(
                $pesanMember,
                $hasil,
                $userId,
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

    /**
     * Track jawaban yang disalin
     */
    public function trackCopy(Request $request)
    {
        $request->validate([
            'memory_id' => 'required|exists:ai_memory,id',
            'tipe_jawaban' => 'required|in:formal,santai,singkat',
        ]);

        try {
            $memory = AiMemory::findOrFail($request->memory_id);

            // Mark sebagai disalin
            $memory->markAsCopied($request->tipe_jawaban);

            return response()->json([
                'sukses' => true,
                'pesan' => 'Jawaban berhasil ditandai sebagai disalin',
                'data' => [
                    'copy_count' => $memory->copy_count,
                    'is_good_example' => $memory->is_good_example,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error track copy', [
                'error' => $e->getMessage(),
                'memory_id' => $request->memory_id,
            ]);

            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal track copy: ' . $e->getMessage(),
            ], 500);
        }
    }
}
