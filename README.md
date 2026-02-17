# 🤖 CS AI Assistant

Aplikasi internal untuk tim Customer Service yang membantu generate jawaban otomatis menggunakan AI.

## 📋 Deskripsi

CS AI Assistant adalah webapp yang dirancang khusus untuk membantu tim Customer Service dalam merespons chat member dengan lebih cepat dan konsisten. Aplikasi ini menggunakan AI (Groq API) untuk menghasilkan 3 versi jawaban berbeda yang bisa langsung digunakan atau diedit sesuai kebutuhan.

## ✨ Fitur Utama

- 🎯 **Generate 3 Versi Jawaban**
  - **Formal** - Bahasa profesional dan resmi
  - **Santai** - Bahasa lebih friendly dan casual
  - **Singkat** - To the point dan efisien

- 🧠 **AI-Powered**
  - Menggunakan Groq API dengan model llama-3.3-70b-versatile
  - System prompt khusus untuk Customer Service
  - Deteksi kategori otomatis (pembayaran, pengiriman, produk, dll)

- 📝 **Fitur Produktivitas**
  - Copy to clipboard dengan satu klik
  - Edit jawaban sebelum disalin
  - Bersihkan form dengan cepat
  - Toast notification untuk feedback

- 💾 **Logging & History**
  - Semua interaksi tersimpan di database
  - Track kategori yang terdeteksi
  - Riwayat jawaban yang di-generate

- 🎨 **UI/UX Modern**
  - Dark mode default
  - Responsive design
  - Loading indicators
  - Error handling yang informatif

## 🛠️ Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Blade Templates, TailwindCSS (CDN), AlpineJS (CDN)
- **Database**: MySQL
- **AI Provider**: Groq API
- **Architecture**: MVC dengan Service Layer

## 🚀 Quick Start

### Prasyarat

- PHP 8.2+
- MySQL 8.0+
- Composer
- Groq API Key (gratis di [console.groq.com](https://console.groq.com))

### Instalasi

```bash
# Clone repository
git clone <repository-url>
cd cs-ai-assistant

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Konfigurasi database di .env
# DB_DATABASE=cs_ai_assistant
# DB_USERNAME=root
# DB_PASSWORD=

# Buat database
mysql -u root -p -e "CREATE DATABASE cs_ai_assistant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migration & seeder
php artisan migrate
php artisan db:seed

# Tambahkan Groq API Key di .env
# GROQ_API_KEY=your_api_key_here

# Jalankan server
php artisan serve
```

Buka browser: http://localhost:8000

### Login Default

- **Email**: cs@example.com
- **Password**: password123

⚠️ **Ganti password setelah login pertama kali!**

## 📖 Dokumentasi Lengkap

- [SETUP.md](SETUP.md) - Panduan instalasi detail & troubleshooting
- [CHANGELOG.md](CHANGELOG.md) - Riwayat perubahan & roadmap

## 🎯 Cara Penggunaan

1. Login dengan kredensial CS
2. Paste chat dari member di kolom kiri
3. Klik tombol "Generate Jawaban"
4. Tunggu AI memproses (biasanya 2-5 detik)
5. Lihat 3 versi jawaban di kolom kanan
6. Edit jika perlu, lalu klik "Salin"
7. Paste ke platform chat member

## 🧪 Testing

### Contoh Chat untuk Testing

```
Halo min, saya sudah transfer tapi belum dikonfirmasi.
Orderan saya nomor #12345. Kapan diproses?
```

AI akan mendeteksi kategori "pembayaran" dan generate 3 versi jawaban yang sesuai.

## 🏗️ Struktur Project

```
app/
├── Http/Controllers/
│   ├── AuthController.php       # Handle login/logout
│   └── DashboardController.php  # Handle generate & log
├── Models/
│   ├── User.php                 # Model user CS
│   ├── Faq.php                  # Model FAQ knowledge base
│   └── LogChat.php              # Model log history
└── Services/
    └── LayananGroq.php          # Service integrasi Groq API

resources/views/
├── layouts/app.blade.php        # Layout utama
├── auth/login.blade.php         # Halaman login
└── dashboard.blade.php          # Halaman dashboard utama

database/
├── migrations/                  # Schema database
└── seeders/                     # Data awal (User & FAQ)
```

## 🔒 Keamanan

- ✅ CSRF protection
- ✅ Password hashing (bcrypt)
- ✅ Input validation
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (Blade escaping)

## 🗺️ Roadmap

### Version 1.1.0
- Halaman riwayat chat lengkap
- Filter & search di riwayat
- Export log ke Excel/CSV
- Dashboard analytics

### Version 1.2.0
- FAQ management CRUD
- Template jawaban custom
- Shortcut keyboard
- Dark/Light mode toggle

## 📝 License

Aplikasi internal untuk penggunaan tim CS. Tidak untuk distribusi publik.

## 🆘 Butuh Bantuan?

Lihat [SETUP.md](SETUP.md) untuk troubleshooting atau hubungi tim developer.

---

**Dibuat dengan ❤️ untuk tim Customer Service**
