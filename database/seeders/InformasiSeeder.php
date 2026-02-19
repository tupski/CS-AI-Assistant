<?php

namespace Database\Seeders;

use App\Models\Informasi;
use Illuminate\Database\Seeder;

class InformasiSeeder extends Seeder
{
    /**
     * Seed data Informasi Umum untuk referensi AI
     */
    public function run(): void
    {
        $informasi = [
            [
                'kategori' => 'pembayaran',
                'judul' => 'Metode Pembayaran',
                'isi' => 'Kami menerima pembayaran melalui transfer bank (BCA, Mandiri, BNI), e-wallet (GoPay, OVO, Dana), dan kartu kredit/debit.',
            ],
            [
                'kategori' => 'pembayaran',
                'judul' => 'Konfirmasi Pembayaran',
                'isi' => 'Setelah melakukan pembayaran, mohon kirimkan bukti transfer ke WhatsApp CS kami atau upload di halaman konfirmasi pembayaran. Pembayaran akan diverifikasi dalam 1x24 jam.',
            ],
            [
                'kategori' => 'pengiriman',
                'judul' => 'Estimasi Pengiriman',
                'isi' => 'Estimasi pengiriman untuk area Jabodetabek 1-2 hari kerja, luar Jabodetabek 2-5 hari kerja tergantung lokasi.',
            ],
            [
                'kategori' => 'pengiriman',
                'judul' => 'Lacak Paket',
                'isi' => 'Nomor resi akan dikirimkan via email dan WhatsApp setelah paket dikirim. Anda bisa melacak paket melalui website ekspedisi atau aplikasi mobile mereka.',
            ],
            [
                'kategori' => 'produk',
                'judul' => 'Stok Produk',
                'isi' => 'Stok produk yang ditampilkan di website adalah real-time. Jika produk masih bisa dimasukkan ke keranjang, berarti stok masih tersedia.',
            ],
            [
                'kategori' => 'retur',
                'judul' => 'Kebijakan Retur',
                'isi' => 'Produk dapat diretur dalam 7 hari setelah diterima jika ada kerusakan atau kesalahan pengiriman. Produk harus dalam kondisi lengkap dan belum digunakan.',
            ],
            [
                'kategori' => 'akun',
                'judul' => 'Lupa Password',
                'isi' => 'Klik "Lupa Password" di halaman login, masukkan email terdaftar, dan kami akan mengirimkan link reset password ke email Anda.',
            ],
            [
                'kategori' => 'promo',
                'judul' => 'Cara Menggunakan Voucher',
                'isi' => 'Masukkan kode voucher di halaman checkout sebelum melakukan pembayaran. Pastikan voucher masih berlaku dan memenuhi syarat minimum pembelian.',
            ],
        ];

        foreach ($informasi as $info) {
            Informasi::create($info);
        }

        $this->command->info('Informasi Umum berhasil di-seed!');
    }
}

