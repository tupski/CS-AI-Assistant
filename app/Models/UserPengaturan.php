<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPengaturan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'user_pengaturan';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'user_id',
        'kunci',
        'nilai',
        'tipe',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper method untuk ambil nilai setting user
     */
    public static function ambil(int $userId, string $kunci, $default = null)
    {
        $setting = self::where('user_id', $userId)
            ->where('kunci', $kunci)
            ->first();

        return $setting ? $setting->nilai : $default;
    }

    /**
     * Helper method untuk simpan/update setting user
     */
    public static function simpan(int $userId, string $kunci, $nilai, string $tipe = 'text')
    {
        return self::updateOrCreate(
            [
                'user_id' => $userId,
                'kunci' => $kunci,
            ],
            [
                'nilai' => $nilai,
                'tipe' => $tipe,
            ]
        );
    }
}
