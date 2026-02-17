<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kategori extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'kategori';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'nama',
        'slug',
        'warna',
        'icon',
        'deskripsi',
        'aktif',
        'urutan',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'aktif' => 'boolean',
        'urutan' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot model untuk auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama') && empty($kategori->slug)) {
                $kategori->slug = Str::slug($kategori->nama);
            }
        });
    }

    /**
     * Relasi ke FAQ
     */
    public function faq(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    /**
     * Scope untuk kategori aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    /**
     * Scope untuk urutan
     */
    public function scopeUrutan($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('nama', 'asc');
    }
}
