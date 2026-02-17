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

## [2.0.0] - 2026-02-17

### ✨ Fitur Baru - Major Update

#### Sistem Multi-Role
- Tabel `roles` untuk manajemen role (Admin, Supervisor, CS)
- Tabel pivot `role_user` untuk relasi many-to-many
- Helper methods di User model: `punyaRole()`, `isAdmin()`, `isSupervisor()`, `isCs()`
- Middleware `CekRole` untuk proteksi route berdasarkan role
- Badge role di navbar untuk identifikasi user

#### Halaman Pengaturan (Admin Only)
- Tab Pengaturan API:
  - Input Groq API Key (disimpan di database)
  - Pilihan model AI (Llama 3.3, Llama 3.1, Mixtral)
  - Validasi dan update real-time
- Tab Manajemen User:
  - Tabel daftar user dengan role
  - Tambah user baru dengan multiple roles
  - Edit user (nama, email, password, roles)
  - Hapus user (dengan proteksi tidak bisa hapus diri sendiri)
  - Modal form dengan AlpineJS

#### Database Settings
- Tabel `pengaturan` untuk menyimpan konfigurasi aplikasi
- Model `Pengaturan` dengan helper methods `ambil()` dan `atur()`
- API key dan model sekarang disimpan di database (bukan hardcode di .env)
- LayananGroq otomatis ambil config dari database dengan fallback ke .env

#### Seeder Updates
- `RoleSeeder` - Seed 3 role default (admin, supervisor, cs)
- `PengaturanSeeder` - Seed pengaturan API default
- `UserSeeder` - Buat 2 user default:
  - Admin (admin@example.com / admin123)
  - CS Staff (cs@example.com / password123)

#### Navigation & UI
- Menu navigasi di navbar (Dashboard, Pengaturan)
- Active state pada menu
- Role badge di user info navbar
- Responsive design untuk mobile

### 🔒 Keamanan
- Middleware role-based access control
- Validasi input untuk semua form
- Password hashing untuk user baru
- CSRF protection di semua form
- Unique constraint untuk email dan role assignment

### 🏗️ Arsitektur
- Migration untuk roles, role_user, dan pengaturan
- Model dengan relasi yang proper
- Controller terpisah untuk pengaturan
- Middleware alias untuk kemudahan penggunaan
- Service layer yang fleksibel (database-first, fallback ke config)

### 📝 Breaking Changes
- User sekarang harus punya minimal 1 role
- API key sekarang prioritas dari database, bukan .env
- Route `/pengaturan/*` hanya bisa diakses admin

---

## Versi Mendatang

### [2.1.0] - Planned

- [ ] Halaman riwayat chat lengkap dengan filter
- [ ] Export log ke Excel/CSV
- [ ] Dashboard analytics untuk supervisor
- [ ] Supervisor bisa lihat performa CS

### [2.2.0] - Planned

- [ ] FAQ management CRUD
- [ ] Template jawaban custom
- [ ] Shortcut keyboard
- [ ] Dark/Light mode toggle
- [ ] Notifikasi real-time

---

**Note**: Untuk detail teknis setiap perubahan, lihat git commit history.

