<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Seed kategori default
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Pembayaran',
                'slug' => 'pembayaran',
                'warna' => 'green',
                'icon' => '💰',
                'deskripsi' => 'Pertanyaan seputar pembayaran, transfer, dan konfirmasi',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'nama' => 'Pengiriman',
                'slug' => 'pengiriman',
                'warna' => 'blue',
                'icon' => '📦',
                'deskripsi' => 'Pertanyaan tentang pengiriman, tracking, dan estimasi',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'nama' => 'Produk',
                'slug' => 'produk',
                'warna' => 'purple',
                'icon' => '🛍️',
                'deskripsi' => 'Pertanyaan tentang produk, stok, dan spesifikasi',
                'aktif' => true,
                'urutan' => 3,
            ],
            [
                'nama' => 'Komplain',
                'slug' => 'komplain',
                'warna' => 'red',
                'icon' => '⚠️',
                'deskripsi' => 'Keluhan dan komplain dari customer',
                'aktif' => true,
                'urutan' => 4,
            ],
            [
                'nama' => 'Umum',
                'slug' => 'umum',
                'warna' => 'yellow',
                'icon' => '💬',
                'deskripsi' => 'Pertanyaan umum lainnya',
                'aktif' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}
