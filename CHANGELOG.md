# Changelog

Semua perubahan penting pada project CS AI Assistant akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.2.0] - 2026-02-17

### ✨ Fitur Baru

#### AI Enhancement
- **AI Belajar dari Peraturan**
  - System prompt AI sekarang include peraturan aktif dari database
  - Peraturan dikelompokkan berdasarkan tipe (Wajib, Larangan, Tips, Umum)
  - Prioritas tinggi ditandai khusus dalam prompt
  - Format peraturan dengan emoji untuk clarity

- **AI Belajar dari Chat History**
  - System prompt include 5 contoh chat terakhir
  - AI belajar gaya bahasa dari jawaban sebelumnya
  - Membantu konsistensi tone dan style

- **AI Guidelines Custom**
  - Tab baru "AI Guidelines" di halaman Pengaturan
  - Admin bisa input panduan tambahan untuk AI
  - Guidelines disimpan di database (key: ai_guidelines)
  - Otomatis ditambahkan ke system prompt AI
  - Textarea besar dengan placeholder contoh

#### Regenerate Jawaban
- **Tombol Regenerate per Tipe**
  - Tombol regenerate di setiap card jawaban (Formal, Santai, Singkat)
  - Loading state per card saat regenerate
  - Hanya update jawaban yang di-regenerate
  - Icon refresh dengan animasi spin
  - Toast notification saat berhasil

#### AJAX Search Peraturan
- **Real-time Search & Filter**
  - Search peraturan dengan debounce 500ms
  - Filter berdasarkan tipe (Umum, Wajib, Larangan, Tips)
  - Filter berdasarkan prioritas (Tinggi, Normal, Rendah)
  - Tombol Reset untuk clear semua filter
  - Loading state saat fetch data
  - Partial view untuk render hasil

### 🔧 Perbaikan

#### LayananGroq Service
- Method `buatSystemPrompt()` sekarang dynamic
- Method `formatPeraturan()` untuk format peraturan by tipe
- Method `formatContohChat()` untuk format chat examples
- Support AI guidelines dari pengaturan

#### PeraturanController
- Support AJAX request dengan JSON response
- Return HTML partial untuk list peraturan
- Filter dan search di backend

#### PengaturanController
- Method `updateGuidelines()` untuk simpan AI guidelines
- Validasi nullable untuk guidelines

#### Dashboard
- Property `loadingRegenerate` untuk track loading state
- Method `regenerate(tipe)` untuk regenerate specific answer
- Update hanya jawaban yang di-regenerate

### 📁 File Baru
- `resources/views/peraturan/partials/list.blade.php` - Partial view untuk AJAX
- `database/seeders/UpdateExistingUserSeeder.php` - Seeder untuk update user existing
- `app/Console/Commands/UpdateUserRole.php` - Command untuk update role user

### 🔄 Routes
- `POST /pengaturan/guidelines` - Route untuk update AI guidelines

### 📝 Dokumentasi
- Update CHANGELOG.md ke versi 2.2.0

## [2.1.0] - 2026-02-17

### ✨ Fitur Baru

#### Content Management System
- **FAQ Management**
  - CRUD lengkap untuk FAQ dengan kategori
  - Filter berdasarkan kategori
  - Search FAQ berdasarkan judul dan isi
  - Pagination untuk daftar FAQ
  - Modal form untuk tambah/edit FAQ
  - Relasi ke kategori dengan foreign key

- **Kategori Jawaban**
  - CRUD lengkap untuk kategori
  - Color picker untuk badge kategori (blue, green, red, yellow, purple)
  - Icon emoji untuk visual kategori
  - Deskripsi dan urutan tampilan
  - Status aktif/nonaktif
  - Auto-generate slug dari nama
  - Proteksi hapus jika masih digunakan di FAQ
  - Grid view dengan preview warna

- **Peraturan & Guidelines CS**
  - CRUD lengkap untuk peraturan CS
  - 4 tipe peraturan: Umum, Wajib, Larangan, Tips
  - 3 level prioritas: Tinggi, Normal, Rendah
  - Grouped display berdasarkan tipe
  - Filter berdasarkan tipe dan prioritas
  - Search peraturan
  - Icon emoji untuk setiap tipe
  - Status aktif/nonaktif

#### Database
- Tabel `kategori` dengan fields: nama, slug, warna, icon, deskripsi, aktif, urutan
- Tabel `peraturan` dengan fields: judul, isi, tipe, prioritas, aktif, urutan
- Foreign key `kategori_id` di tabel `faq`
- Migration untuk backward compatibility

#### Models & Controllers
- Model `Kategori` dengan auto-slug generation dan query scopes
- Model `Peraturan` dengan query scopes untuk filter
- Update model `Faq` dengan relasi ke Kategori
- `FaqController` dengan CRUD dan filter
- `KategoriController` dengan CRUD dan validasi
- `PeraturanController` dengan CRUD dan grouping

#### UI/UX
- Halaman FAQ dengan table view dan modal form
- Halaman Kategori dengan grid card view
- Halaman Peraturan dengan grouped display
- Color-coded badges untuk kategori
- Priority badges untuk peraturan
- Filter dan search di semua halaman
- Responsive design untuk semua halaman baru

#### Routes & Navigation
- Route resource untuk FAQ (admin & supervisor)
- Route resource untuk Kategori (admin only)
- Route untuk Peraturan (semua bisa lihat, admin bisa edit)
- Menu navigasi di navbar:
  - FAQ (admin & supervisor)
  - Kategori (admin only)
  - Peraturan (semua role)

#### Seeders
- `KategoriSeeder` dengan 5 kategori default:
  - Pembayaran (💰 green)
  - Pengiriman (📦 blue)
  - Produk (🛍️ purple)
  - Komplain (⚠️ red)
  - Umum (💬 yellow)
- `PeraturanSeeder` dengan 11 peraturan default:
  - 3 peraturan wajib
  - 3 larangan
  - 3 tips
  - 2 peraturan umum

### 🔧 Perbaikan
- Update navigation menu dengan menu baru
- Update DatabaseSeeder untuk include seeder baru
- Backward compatibility untuk field kategori string di FAQ

### 📝 Dokumentasi
- Update README.md dengan fitur baru
- Update SETUP.md dengan instruksi baru
- Update CHANGELOG.md ke versi 2.1.0

## [2.0.0] - 2026-02-17

### ✨ Fitur Baru

#### Multi-Role System
- Sistem role many-to-many (users ↔ roles)
- 3 role: Admin, Supervisor, Customer Service
- User bisa punya multiple roles
- Helper methods di User model: `punyaRole()`, `isAdmin()`, `isSupervisor()`, `isCs()`
- Middleware `CekRole` untuk proteksi route
- Role badges di navbar

#### Halaman Pengaturan (Admin Only)
- **Tab Pengaturan API**:
  - Input Groq API Key dari UI
  - Pilih model AI (Llama 3.3, Llama 3.1, Mixtral)
  - Simpan ke database (tidak perlu edit .env)
- **Tab Manajemen User**:
  - Lihat daftar user dengan roles
  - Tambah user baru dengan multiple roles
  - Edit user (nama, email, password, roles)
  - Hapus user (dengan proteksi)
  - Modal form interaktif

#### Database
- Tabel `roles` untuk master role
- Tabel `role_user` untuk pivot many-to-many
- Tabel `pengaturan` untuk konfigurasi aplikasi
- Migration dengan relasi yang proper

#### Models & Controllers
- Model `Role` dengan relasi ke User
- Model `Pengaturan` dengan helper methods `ambil()` dan `atur()`
- Update model `User` dengan relasi roles
- `PengaturanController` untuk CRUD settings dan user
- Middleware `CekRole` untuk authorization

#### Integrasi
- `LayananGroq` ambil API key dari database (fallback ke .env)
- Route protection dengan middleware role
- Navigation menu dengan active state

### 🔧 Perbaikan
- Update seeder untuk roles dan pengaturan
- Update layout dengan role badges
- Improved security dengan role-based access

### 📝 Dokumentasi
- Update README.md dengan fitur multi-role
- Update SETUP.md dengan login credentials baru
- Update CHANGELOG.md ke versi 2.0.0

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

