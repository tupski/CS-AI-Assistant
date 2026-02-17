<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiMemory extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'ai_memory';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'pesan_member',
        'kategori_terdeteksi',
        'jawaban_formal',
        'jawaban_santai',
        'jawaban_singkat',
        'system_prompt_used',
        'peraturan_used',
        'faq_used',
        'provider_digunakan',
        'user_id',
        'is_good_example',
        'usage_count',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'peraturan_used' => 'array',
        'faq_used' => 'array',
        'is_good_example' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk ambil contoh yang bagus
     */
    public function scopeGoodExamples($query)
    {
        return $query->where('is_good_example', true);
    }

    /**
     * Scope untuk ambil berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_terdeteksi', $kategori);
    }

    /**
     * Scope untuk ambil yang paling sering digunakan
     */
    public function scopeMostUsed($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}
