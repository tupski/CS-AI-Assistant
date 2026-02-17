<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class AiProvider extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'ai_provider';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'user_id',
        'nama',
        'tipe',
        'model',
        'api_key',
        'api_url',
        'aktif',
        'prioritas',
        'quota_limit',
        'quota_used',
        'quota_reset_date',
        'error_count',
        'last_used_at',
        'last_error_at',
        'last_error_message',
        'config',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'aktif' => 'boolean',
        'prioritas' => 'integer',
        'quota_limit' => 'integer',
        'quota_used' => 'integer',
        'error_count' => 'integer',
        'quota_reset_date' => 'date',
        'last_used_at' => 'datetime',
        'last_error_at' => 'datetime',
        'config' => 'array',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Encrypt API key saat set
     */
    public function setApiKeyAttribute($value)
    {
        if ($value) {
            $this->attributes['api_key'] = Crypt::encryptString($value);
        }
    }

    /**
     * Decrypt API key saat get
     */
    public function getApiKeyAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Scope: Provider yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope: Provider global (bukan milik user tertentu)
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Scope: Provider milik user tertentu
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Provider yang tersedia (aktif dan punya API key)
     */
    public function scopeTersedia($query)
    {
        return $query->where('aktif', true)
            ->whereNotNull('api_key');
    }

    /**
     * Scope: Urutkan berdasarkan prioritas
     */
    public function scopeByPrioritas($query)
    {
        return $query->orderBy('prioritas', 'asc')
            ->orderBy('last_used_at', 'asc'); // Yang jarang dipakai prioritas
    }

    /**
     * Cek apakah provider masih punya quota
     */
    public function punyaQuota(): bool
    {
        // Reset quota jika sudah lewat tanggal reset
        $this->resetQuotaJikaPerlu();

        // Jika tidak ada limit, berarti unlimited
        if ($this->quota_limit === null) {
            return true;
        }

        return $this->quota_used < $this->quota_limit;
    }

    /**
     * Reset quota jika sudah lewat tanggal reset
     */
    public function resetQuotaJikaPerlu(): void
    {
        if ($this->quota_reset_date && $this->quota_reset_date->isPast()) {
            $this->update([
                'quota_used' => 0,
                'quota_reset_date' => now()->addDay(),
                'error_count' => 0,
            ]);
        }
    }

    /**
     * Increment quota usage
     */
    public function incrementQuota(): void
    {
        $this->increment('quota_used');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Catat error
     */
    public function catatError(string $message): void
    {
        $this->update([
            'error_count' => $this->error_count + 1,
            'last_error_at' => now(),
            'last_error_message' => $message,
        ]);

        // Auto-disable jika error berturut-turut > 5
        if ($this->error_count >= 5) {
            $this->update(['aktif' => false]);
        }
    }

    /**
     * Reset error count
     */
    public function resetError(): void
    {
        $this->update([
            'error_count' => 0,
            'last_error_message' => null,
        ]);
    }

    /**
     * Get provider yang tersedia untuk user (user provider + global provider)
     */
    public static function getAvailableProviders(?int $userId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = self::tersedia()->byPrioritas();

        if ($userId) {
            // Ambil provider user + global
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', $userId)
                    ->orWhereNull('user_id');
            });
        } else {
            // Hanya global
            $query->whereNull('user_id');
        }

        return $query->get();
    }

    /**
     * Get provider terbaik untuk digunakan (dengan quota dan rotasi)
     */
    public static function getBestProvider(?int $userId = null): ?self
    {
        $providers = self::getAvailableProviders($userId);

        foreach ($providers as $provider) {
            if ($provider->punyaQuota()) {
                return $provider;
            }
        }

        return null; // Semua provider habis quota
    }
}
