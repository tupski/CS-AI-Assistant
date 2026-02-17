<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogChat extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'log_chat';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'pesan_member',
        'kategori_terdeteksi',
        'jawaban_formal',
        'jawaban_santai',
        'jawaban_singkat',
        'provider_digunakan',
        'user_id',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (CS yang generate)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
