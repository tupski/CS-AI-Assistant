<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'kategori',
        'judul',
        'isi',
    ];

    /**
     * Cast tipe data
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
