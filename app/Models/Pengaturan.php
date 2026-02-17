<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    /**
     * Nama tabel di database
     */
    protected $table = 'pengaturan';

    /**
     * Field yang boleh diisi mass assignment
     */
    protected $fillable = [
        'kunci',
        'nilai',
        'tipe',
        'grup',
        'label',
        'deskripsi',
    ];

    /**
     * Helper untuk ambil nilai setting berdasarkan kunci
     *
     * @param string $kunci Kunci setting
     * @param mixed $default Nilai default jika tidak ada
     * @return mixed
     */
    public static function ambil(string $kunci, $default = null)
    {
        $setting = self::where('kunci', $kunci)->first();
        return $setting ? $setting->nilai : $default;
    }

    /**
     * Helper untuk set nilai setting
     *
     * @param string $kunci Kunci setting
     * @param mixed $nilai Nilai setting
     * @return bool
     */
    public static function atur(string $kunci, $nilai): bool
    {
        return self::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai]
        ) ? true : false;
    }
}
