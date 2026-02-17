<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peraturan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'peraturan';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'judul',
        'isi',
        'tipe',
        'prioritas',
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
     * Scope untuk peraturan aktif
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
        return $query->orderBy('urutan', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Scope berdasarkan tipe
     */
    public function scopeTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    /**
     * Scope berdasarkan prioritas
     */
    public function scopePrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }
}
