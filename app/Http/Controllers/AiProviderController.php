<?php

namespace App\Http\Controllers;

use App\Models\AiProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiProviderController extends Controller
{
    /**
     * Tampilkan halaman pengaturan AI Provider
     */
    public function index()
    {
        $userId = Auth::id();

        // Ambil provider global dan user
        $globalProviders = AiProvider::global()->byPrioritas()->get();
        $userProviders = AiProvider::byUser($userId)->byPrioritas()->get();

        return view('ai-provider.index', compact('globalProviders', 'userProviders'));
    }

    /**
     * Update API key provider
     */
    public function updateApiKey(Request $request, $id)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);

        $provider = AiProvider::findOrFail($id);

        // Cek permission: hanya bisa update global provider (admin) atau user provider sendiri
        if ($provider->user_id && $provider->user_id !== Auth::id()) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Anda tidak memiliki akses untuk mengubah provider ini',
            ], 403);
        }

        $provider->update([
            'api_key' => $request->api_key,
        ]);

        return response()->json([
            'sukses' => true,
            'pesan' => 'API key berhasil diupdate',
        ]);
    }

    /**
     * Toggle aktif/nonaktif provider
     */
    public function toggleAktif($id)
    {
        $provider = AiProvider::findOrFail($id);

        // Cek permission
        if ($provider->user_id && $provider->user_id !== Auth::id()) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Anda tidak memiliki akses untuk mengubah provider ini',
            ], 403);
        }

        $provider->update([
            'aktif' => !$provider->aktif,
        ]);

        return response()->json([
            'sukses' => true,
            'pesan' => $provider->aktif ? 'Provider diaktifkan' : 'Provider dinonaktifkan',
            'aktif' => $provider->aktif,
        ]);
    }

    /**
     * Update prioritas provider
     */
    public function updatePrioritas(Request $request, $id)
    {
        $request->validate([
            'prioritas' => 'required|integer|min:0',
        ]);

        $provider = AiProvider::findOrFail($id);

        // Cek permission
        if ($provider->user_id && $provider->user_id !== Auth::id()) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Anda tidak memiliki akses untuk mengubah provider ini',
            ], 403);
        }

        $provider->update([
            'prioritas' => $request->prioritas,
        ]);

        return response()->json([
            'sukses' => true,
            'pesan' => 'Prioritas berhasil diupdate',
        ]);
    }

    /**
     * Update quota limit
     */
    public function updateQuota(Request $request, $id)
    {
        $request->validate([
            'quota_limit' => 'nullable|integer|min:0',
        ]);

        $provider = AiProvider::findOrFail($id);

        // Cek permission
        if ($provider->user_id && $provider->user_id !== Auth::id()) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Anda tidak memiliki akses untuk mengubah provider ini',
            ], 403);
        }

        $provider->update([
            'quota_limit' => $request->quota_limit ?: null,
        ]);

        return response()->json([
            'sukses' => true,
            'pesan' => 'Quota limit berhasil diupdate',
        ]);
    }

    /**
     * Reset quota usage
     */
    public function resetQuota($id)
    {
        $provider = AiProvider::findOrFail($id);

        // Cek permission
        if ($provider->user_id && $provider->user_id !== Auth::id()) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Anda tidak memiliki akses untuk mengubah provider ini',
            ], 403);
        }

        $provider->update([
            'quota_used' => 0,
            'quota_reset_date' => now()->addDay(),
        ]);

        return response()->json([
            'sukses' => true,
            'pesan' => 'Quota berhasil direset',
        ]);
    }

    /**
     * Get usage statistics untuk semua provider
     */
    public function getUsageStats()
    {
        $userId = Auth::id();
        $providers = AiProvider::getAvailableProviders($userId);

        $stats = $providers->map(function ($provider) {
            $quotaPercentage = 0;
            if ($provider->quota_limit && $provider->quota_limit > 0) {
                $quotaPercentage = round(($provider->quota_used / $provider->quota_limit) * 100, 2);
            }

            return [
                'id' => $provider->id,
                'nama' => $provider->nama,
                'model' => $provider->model,
                'tipe' => $provider->tipe,
                'aktif' => $provider->aktif,
                'prioritas' => $provider->prioritas,
                'quota_limit' => $provider->quota_limit,
                'quota_used' => $provider->quota_used,
                'quota_percentage' => $quotaPercentage,
                'quota_remaining' => $provider->quota_limit ? ($provider->quota_limit - $provider->quota_used) : null,
                'quota_reset_date' => $provider->quota_reset_date,
                'error_count' => $provider->error_count,
                'last_used_at' => $provider->last_used_at,
                'last_error_at' => $provider->last_error_at,
                'last_error_message' => $provider->last_error_message,
                'has_api_key' => !empty($provider->api_key),
                'is_user_provider' => $provider->user_id !== null,
            ];
        });

        return response()->json([
            'sukses' => true,
            'data' => $stats,
            'total_providers' => $stats->count(),
            'active_providers' => $stats->where('aktif', true)->count(),
        ]);
    }
}
