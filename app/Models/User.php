<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke LogChat (history chat yang di-generate user ini)
     */
    public function logChat(): HasMany
    {
        return $this->hasMany(LogChat::class);
    }

    /**
     * Relasi many-to-many ke Role
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    /**
     * Cek apakah user punya role tertentu
     *
     * @param string $roleName Nama role (admin, supervisor, cs)
     * @return bool
     */
    public function punyaRole(string $roleName): bool
    {
        return $this->roles()->where('nama', $roleName)->exists();
    }

    /**
     * Cek apakah user punya salah satu dari beberapa role
     *
     * @param array $roleNames Array nama role
     * @return bool
     */
    public function punyaSalahSatuRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('nama', $roleNames)->exists();
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->punyaRole('admin');
    }

    /**
     * Cek apakah user adalah supervisor
     */
    public function isSupervisor(): bool
    {
        return $this->punyaRole('supervisor');
    }

    /**
     * Cek apakah user adalah CS
     */
    public function isCs(): bool
    {
        return $this->punyaRole('cs');
    }
}
