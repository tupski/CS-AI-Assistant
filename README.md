# CS AI Assistant

Webapp internal untuk membantu tim Customer Service dalam merespons chat member dengan bantuan AI.

## 📋 Deskripsi

CS AI Assistant adalah aplikasi web yang dirancang khusus untuk tim Customer Service. Aplikasi ini memungkinkan CS untuk:

- Paste chat dari member
- Generate 3 versi jawaban otomatis menggunakan AI (Formal, Santai, Singkat)
- Copy jawaban dengan cepat
- Menyimpan log percakapan ke database untuk analisis

## 🛠️ Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Blade Templates, TailwindCSS, AlpineJS
- **Database**: MySQL
- **AI Provider**: Groq API

## ✨ Fitur Utama

1. **Autentikasi Sederhana** - Login/logout untuk tim CS
2. **Dashboard Interaktif** - Interface 2 kolom yang intuitif
3. **AI-Powered Response** - Generate 3 versi jawaban berbeda
4. **Quick Copy** - Salin jawaban dengan satu klik
5. **Edit Before Copy** - Modifikasi jawaban sebelum disalin
6. **Auto Logging** - Semua interaksi tersimpan otomatis

## 📦 Instalasi

```bash
# Clone repository
git clone <repository-url>
cd cs-ai-assistant

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
# Edit .env dengan kredensial database Anda
php artisan migrate

# Compile assets
npm run dev

# Jalankan server
php artisan serve
```

## ⚙️ Konfigurasi

Edit file `.env` dan tambahkan:

```env
GROQ_API_KEY=your_groq_api_key_here
```

## 🚀 Cara Menggunakan

1. Login dengan akun CS
2. Paste chat member di kolom kiri
3. Klik "Generate Jawaban"
4. Pilih versi jawaban yang sesuai (Formal/Santai/Singkat)
5. Edit jika perlu, lalu klik "Salin"

## 📝 Lisensi

Internal use only - Tim Customer Service

## 👨‍💻 Developer

Dibuat dengan ❤️ untuk tim CS

