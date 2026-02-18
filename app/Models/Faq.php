<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'faq';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'kategori_id',
        'kategori',
        'judul',
        'isi',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'kategori_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Kategori
     */
    public function kategoriRelasi(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Accessor untuk kategori (backward compatibility)
     * Prioritaskan relasi jika ada kategori_id
     */
    public function getKategoriAttribute($value)
    {
        // Jika ada kategori_id, gunakan relasi
        if ($this->kategori_id) {
            return $this->kategoriRelasi;
        }

        // Jika tidak, return string value dari database
        return $value;
    }
}
