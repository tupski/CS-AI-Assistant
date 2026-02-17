<?php

namespace Database\Seeders;

use App\Models\Peraturan;
use Illuminate\Database\Seeder;

class PeraturanSeeder extends Seeder
{
    /**
     * Seed peraturan default untuk CS
     */
    public function run(): void
    {
        $peraturans = [
            // Wajib
            [
                'judul' => 'Selalu gunakan bahasa yang sopan dan profesional',
                'isi' => 'Dalam setiap interaksi dengan customer, gunakan bahasa yang sopan, ramah, dan profesional. Hindari bahasa kasar atau tidak pantas.',
                'tipe' => 'wajib',
                'prioritas' => 'tinggi',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Respon customer maksimal 5 menit',
                'isi' => 'Usahakan untuk merespon setiap pertanyaan customer dalam waktu maksimal 5 menit. Jika membutuhkan waktu lebih lama, beri tahu customer bahwa Anda sedang mengecek informasi.',
                'tipe' => 'wajib',
                'prioritas' => 'tinggi',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Konfirmasi pemahaman sebelum menutup chat',
                'isi' => 'Sebelum menutup percakapan, pastikan customer sudah memahami jawaban yang diberikan dengan bertanya "Apakah ada yang bisa saya bantu lagi?"',
                'tipe' => 'wajib',
                'prioritas' => 'normal',
                'aktif' => true,
                'urutan' => 3,
            ],

            // Larangan
            [
                'judul' => 'Jangan memberikan informasi yang tidak pasti',
                'isi' => 'Jangan memberikan jawaban atau informasi yang tidak Anda yakini kebenarannya. Lebih baik katakan "Mohon tunggu sebentar, saya akan cek terlebih dahulu" daripada memberikan informasi yang salah.',
                'tipe' => 'larangan',
                'prioritas' => 'tinggi',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Jangan mengabaikan komplain customer',
                'isi' => 'Setiap komplain harus ditanggapi dengan serius. Jangan pernah mengabaikan atau meremehkan keluhan customer, sekecil apapun itu.',
                'tipe' => 'larangan',
                'prioritas' => 'tinggi',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Jangan gunakan emoji berlebihan',
                'isi' => 'Gunakan emoji secukupnya untuk menjaga profesionalitas. Maksimal 1-2 emoji per pesan.',
                'tipe' => 'larangan',
                'prioritas' => 'rendah',
                'aktif' => true,
                'urutan' => 3,
            ],

            // Tips
            [
                'judul' => 'Gunakan template jawaban untuk efisiensi',
                'isi' => 'Manfaatkan fitur AI untuk generate jawaban cepat, lalu sesuaikan dengan konteks spesifik customer. Ini akan menghemat waktu dan menjaga konsistensi.',
                'tipe' => 'tips',
                'prioritas' => 'normal',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Personalisasi dengan menyebut nama customer',
                'isi' => 'Jika customer menyebutkan namanya, gunakan nama tersebut dalam percakapan untuk membuat interaksi lebih personal dan ramah.',
                'tipe' => 'tips',
                'prioritas' => 'normal',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'judul' => 'Catat informasi penting di log',
                'isi' => 'Semua interaksi penting dengan customer akan otomatis tersimpan di log. Pastikan untuk menggunakan fitur generate jawaban agar tercatat dengan baik.',
                'tipe' => 'tips',
                'prioritas' => 'rendah',
                'aktif' => true,
                'urutan' => 3,
            ],

            // Umum
            [
                'judul' => 'Jam operasional CS: 08:00 - 22:00 WIB',
                'isi' => 'Customer Service beroperasi setiap hari dari jam 08:00 pagi hingga 22:00 malam WIB. Di luar jam tersebut, customer akan mendapat auto-reply.',
                'tipe' => 'umum',
                'prioritas' => 'normal',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'judul' => 'Eskalasi ke supervisor untuk kasus kompleks',
                'isi' => 'Jika menemui kasus yang kompleks atau di luar kewenangan Anda, segera eskalasi ke supervisor dengan memberikan ringkasan situasi.',
                'tipe' => 'umum',
                'prioritas' => 'normal',
                'aktif' => true,
                'urutan' => 2,
            ],
        ];

        foreach ($peraturans as $peraturan) {
            Peraturan::create($peraturan);
        }
    }
}
