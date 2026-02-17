# Setup CS AI Assistant

Panduan lengkap untuk setup dan menjalankan aplikasi CS AI Assistant.

## 📋 Prasyarat

- PHP 8.2 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Composer
- Groq API Key (gratis di https://console.groq.com)

## 🚀 Langkah Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd cs-ai-assistant
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
# Copy file .env.example ke .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan dengan kredensial database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cs_ai_assistant
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Buat Database

```bash
# Buat database MySQL
mysql -u root -p -e "CREATE DATABASE cs_ai_assistant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. Seed Data Awal

```bash
php artisan db:seed
```

Ini akan membuat:
- User CS default: `cs@example.com` / `password123`
- Data FAQ contoh

### 8. Setup Groq API

1. Daftar di https://console.groq.com (gratis)
2. Buat API Key
3. Edit file `.env` dan tambahkan:

```env
GROQ_API_KEY=your_api_key_here
```

### 9. Jalankan Server

```bash
php artisan serve
```

Aplikasi akan berjalan di: http://localhost:8000

## 🔐 Login

### Admin User (Akses Penuh)
- **Email**: admin@example.com
- **Password**: admin123
- **Role**: Admin
- **Akses**: Dashboard + Pengaturan

### CS User (Akses Terbatas)
- **Email**: cs@example.com
- **Password**: password123
- **Role**: Customer Service
- **Akses**: Dashboard saja

⚠️ **PENTING**: Ganti password default setelah login pertama kali!

## 🧪 Testing

### Test Manual - Dashboard

1. Login dengan salah satu kredensial di atas
2. Paste contoh chat member di kolom kiri
3. Klik "Generate Jawaban"
4. Lihat 3 versi jawaban di kolom kanan
5. Edit jika perlu, lalu klik "Salin"

### Test Manual - Pengaturan (Admin Only)

1. Login sebagai admin
2. Klik menu "Pengaturan" di navbar
3. Tab "Pengaturan API":
   - Input Groq API Key Anda
   - Pilih model yang diinginkan
   - Klik "Simpan Pengaturan API"
4. Tab "Manajemen User":
   - Klik "Tambah User" untuk buat user baru
   - Pilih role yang sesuai (bisa multiple)
   - Edit atau hapus user yang ada

### Contoh Chat Member untuk Testing

```
Halo min, saya sudah transfer tapi belum dikonfirmasi.
Orderan saya nomor #12345. Kapan diproses?
```

## 📝 Troubleshooting

### Error: SQLSTATE[HY000] [2002] Connection refused

MySQL belum running. Jalankan MySQL terlebih dahulu.

### Error: Groq API Error 401

API Key salah atau belum diset. Cek file `.env`.

### Error: Class 'App\Services\LayananGroq' not found

Jalankan:
```bash
composer dump-autoload
```

## 🔄 Reset Database

Jika ingin reset database dari awal:

```bash
php artisan migrate:fresh --seed
```

⚠️ **WARNING**: Ini akan menghapus semua data!

## 📚 Dokumentasi Tambahan

- [README.md](README.md) - Informasi umum project
- [CHANGELOG.md](CHANGELOG.md) - Riwayat perubahan

## 💡 Tips

1. **API Key**: Sekarang bisa diatur lewat halaman Pengaturan (tidak perlu edit .env lagi)
2. **Multi-Role**: User bisa punya lebih dari 1 role sekaligus
3. **Model AI**: Gunakan `llama-3.3-70b-versatile` untuk hasil terbaik
4. **Keamanan**: Simpan API key dengan aman, jangan commit ke git
5. **Backup**: Backup database secara berkala
6. **Monitoring**: Monitor log di `storage/logs/laravel.log`
7. **Role Admin**: Hanya admin yang bisa akses halaman Pengaturan

## 🆘 Butuh Bantuan?

Hubungi tim developer atau buat issue di repository.

