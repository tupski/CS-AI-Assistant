<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'roles';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'nama',
        'label',
        'deskripsi',
    ];

    /**
     * Relasi many-to-many ke User
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
