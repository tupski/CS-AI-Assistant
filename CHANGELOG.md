# Changelog

Semua perubahan penting pada project CS AI Assistant akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-17

### ✨ Fitur Baru

#### Autentikasi
- Sistem login/logout sederhana untuk CS
- User seeder dengan kredensial default
- Session management dengan Laravel Auth

#### Dashboard Interaktif
- Layout 2 kolom (input kiri, output kanan)
- Dark mode default dengan TailwindCSS
- Integrasi AlpineJS untuk interaktivitas
- Real-time feedback dengan loading state

#### AI Integration
- Integrasi dengan Groq API (llama-3.3-70b-versatile)
- System prompt khusus untuk Customer Service
- Generate 3 versi jawaban:
  - Formal - Bahasa profesional
  - Santai - Bahasa lebih friendly
  - Singkat - To the point
- Deteksi kategori otomatis

#### Fitur Dashboard
- Paste chat member di textarea
- Generate jawaban dengan satu klik
- Edit jawaban sebelum disalin
- Copy to clipboard dengan notifikasi toast
- Tombol bersihkan untuk reset form
- Error handling yang informatif

#### Database & Logging
- Tabel `users` untuk autentikasi
- Tabel `faq` untuk referensi knowledge base
- Tabel `log_chat` untuk menyimpan history
- Relasi Eloquent yang proper
- Seeder untuk data awal (User & FAQ)

#### UI/UX
- Design dark mode yang profesional
- Responsive layout
- Loading indicators
- Success/error notifications
- Toast messages untuk feedback
- Color-coded cards (Blue, Green, Orange)

### 🏗️ Arsitektur

- **Backend**: Laravel 12
- **Frontend**: Blade Templates + TailwindCSS + AlpineJS
- **Database**: MySQL dengan migration
- **AI Provider**: Groq API
- **Pattern**: MVC dengan Service Layer

### 📁 Struktur Project

```
app/
├── Http/Controllers/
│   ├── AuthController.php
│   └── DashboardController.php
├── Models/
│   ├── User.php
│   ├── Faq.php
│   └── LogChat.php
└── Services/
    └── LayananGroq.php

resources/views/
├── layouts/
│   └── app.blade.php
├── components/
│   └── card-jawaban.blade.php
├── auth/
│   └── login.blade.php
└── dashboard.blade.php

database/
├── migrations/
│   ├── 2026_02_17_030952_buat_tabel_faq.php
│   └── 2026_02_17_030954_buat_tabel_log_chat.php
└── seeders/
    ├── UserSeeder.php
    └── FaqSeeder.php
```

### 📝 Dokumentasi

- README.md - Overview project
- SETUP.md - Panduan instalasi lengkap
- CHANGELOG.md - Riwayat perubahan

### 🔒 Keamanan

- CSRF protection di semua form
- Password hashing dengan bcrypt
- Input validation
- SQL injection protection (Eloquent ORM)
- XSS protection (Blade escaping)

### 🎯 Kode Berkualitas

- Komentar dalam Bahasa Indonesia yang natural
- Clean code dengan separation of concerns
- Service layer untuk business logic
- Reusable components
- Error handling yang comprehensive

### 🚀 Deployment Ready

- Environment configuration via .env
- Database migration system
- Seeder untuk data awal
- Git version control
- Dokumentasi lengkap

---

## Versi Mendatang

### [1.1.0] - Planned

- [ ] Halaman riwayat chat lengkap
- [ ] Filter dan search di riwayat
- [ ] Export log ke Excel/CSV
- [ ] Dashboard analytics
- [ ] Multi-user management
- [ ] Role & permission system

### [1.2.0] - Planned

- [ ] FAQ management CRUD
- [ ] Template jawaban custom
- [ ] Shortcut keyboard
- [ ] Dark/Light mode toggle
- [ ] Notifikasi real-time

---

**Note**: Untuk detail teknis setiap perubahan, lihat git commit history.

