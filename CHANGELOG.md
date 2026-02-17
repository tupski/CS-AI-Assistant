# Changelog

Semua perubahan penting pada project CS AI Assistant akan didokumentasikan di file ini.

Format berdasarkan [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
dan project ini mengikuti [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.4.0] - 2026-02-18

### ✨ Fitur Baru - Multi-Provider AI System

#### Sistem Multi-Provider dengan Auto-Rotation
- **Tabel AI Provider**
  - Tabel baru `ai_provider` untuk manage multiple AI providers
  - Support 4 provider: Groq, OpenAI, Google Gemini, Anthropic Claude
  - Field: nama, tipe, model, api_key (encrypted), api_url
  - Quota management: quota_limit, quota_used, quota_reset_date
  - Error tracking: error_count, last_error_at, last_error_message
  - Priority system untuk rotation order
  - Config JSON untuk temperature, max_tokens, dll
  - User-specific dan global providers

- **Model AiProvider**
  - Auto encryption/decryption API key menggunakan Laravel Crypt
  - Method `punyaQuota()` untuk cek ketersediaan quota
  - Method `resetQuotaJikaPerlu()` untuk auto-reset quota harian
  - Method `incrementQuota()` untuk tracking usage
  - Method `catatError()` untuk log errors
  - Auto-disable provider setelah 5 error berturut-turut
  - Static method `getBestProvider()` untuk rotation logic
  - Static method `getAvailableProviders()` untuk filter by user

- **Service LayananAI Universal**
  - Refactor dari LayananGroq menjadi LayananAI universal
  - Support multiple providers dengan adapter pattern
  - Provider-specific API calls:
    - `callGroq()` - Groq API (OpenAI-compatible)
    - `callOpenAI()` - OpenAI GPT-4o, GPT-4o-mini
    - `callGemini()` - Google Gemini 2.0 Flash
    - `callAnthropic()` - Anthropic Claude 3.5 Sonnet
  - Auto-rotation saat quota habis atau error
  - Method `retryWithNextProvider()` untuk failover otomatis
  - Inherit semua method dari LayananGroq (formatPeraturan, formatFaq, dll)
  - Support user context untuk personalized settings

- **Pilih Model AI saat Generate**
  - Dropdown di dashboard untuk pilih provider/model
  - Hanya tampilkan provider yang ada API key-nya
  - Option "Auto (Rotasi Otomatis)" untuk smart selection
  - Display quota usage di dropdown
  - Provider yang dipilih disimpan di log chat

- **Auto-Rotation System**
  - Otomatis switch ke provider lain jika quota habis
  - Otomatis switch jika provider error
  - Priority-based rotation (angka terkecil = prioritas tertinggi)
  - Logging untuk tracking rotation events
  - Graceful fallback dengan error message informatif

- **UI Pengaturan AI Provider**
  - Halaman `/ai-provider` untuk manage providers
  - Section Global Providers (untuk semua user)
  - Section Personal Providers (user-specific)
  - Fitur per provider:
    - Update API key dengan show/hide toggle
    - Enable/disable provider
    - Quota usage dengan progress bar visual
    - Reset quota button
    - Display last used timestamp
    - Display error count dan last error message
    - Color-coded quota indicator (green/yellow/red)
  - Info box dengan penjelasan cara kerja auto-rotation
  - Toast notification untuk feedback

- **Quota Monitoring Dashboard**
  - Widget stats di dashboard utama
  - Display 4 provider teratas dengan status
  - Real-time quota usage dengan progress bar
  - Active/inactive indicator
  - Link ke halaman pengaturan AI Provider
  - Endpoint `/ai-provider/stats` untuk get usage statistics

- **AiProviderSeeder**
  - Seeder dengan 6 default providers:
    1. Groq - Llama 3.3 70B (active, unlimited)
    2. Groq - Llama 3.1 8B (inactive)
    3. Google Gemini 2.0 Flash (inactive, 1500/day limit)
    4. OpenAI GPT-4o (inactive)
    5. OpenAI GPT-4o Mini (inactive)
    6. Anthropic Claude 3.5 Sonnet (inactive)
  - Default config: temperature 0.7, max_tokens 2000

### 🔧 Perubahan

- **DashboardController**
  - Update untuk support multi-provider
  - Validasi `provider_id` parameter
  - Pass selected provider ke LayananAI
  - Track provider yang digunakan di log chat
  - Pass available providers ke view

- **Navigation Menu**
  - Tambah menu "AI Provider" di navbar
  - Accessible untuk semua user (bukan hanya admin)
  - Active state indicator

### 📚 API Endpoints Baru

- `GET /ai-provider` - Halaman pengaturan AI Provider
- `GET /ai-provider/stats` - Get usage statistics semua provider
- `POST /ai-provider/{id}/api-key` - Update API key provider
- `POST /ai-provider/{id}/toggle` - Toggle aktif/nonaktif provider
- `POST /ai-provider/{id}/prioritas` - Update prioritas rotation
- `POST /ai-provider/{id}/quota` - Update quota limit
- `POST /ai-provider/{id}/reset-quota` - Reset quota usage

### 🎨 UI/UX Improvements

- Provider selection dropdown di dashboard
- Quota usage visualization dengan color-coded progress bars
- Real-time stats widget di dashboard
- Modern card-based layout untuk provider management
- Show/hide API key toggle untuk security
- Toast notifications untuk user feedback
- Responsive grid layout (1 kolom mobile, 2 kolom desktop)

### 🔒 Security

- API key encryption menggunakan Laravel Crypt
- Permission checking: user hanya bisa edit provider sendiri
- Masked API key display dengan show/hide toggle
- CSRF protection untuk semua endpoints

### 📊 Monitoring & Analytics

- Usage tracking per provider
- Error counting dan logging
- Last used timestamp
- Quota percentage calculation
- Provider health status
- Auto-disable unhealthy providers

---

## [2.3.0] - 2026-02-17

### ✨ Fitur Baru

#### AI Memory & Learning System
- **Tabel AI Memory**
  - Tabel baru `ai_memory` untuk menyimpan context learning AI
  - Setiap generate jawaban otomatis disimpan sebagai memory
  - Field: pesan_member, kategori_terdeteksi, 3 versi jawaban
  - Snapshot system_prompt_used, peraturan_used, faq_used (JSON)
  - Tracking usage_count untuk monitor referensi
  - Flag is_good_example untuk filter quality

- **AI Belajar dari Memory**
  - System prompt sekarang include 5 contoh jawaban terbaik dari AI Memory
  - AI belajar pola dan gaya bahasa dari jawaban sebelumnya
  - Usage count otomatis increment saat memory digunakan
  - Memory diurutkan berdasarkan latest untuk freshness

- **AI Belajar dari FAQ**
  - System prompt include 10 FAQ dari knowledge base
  - FAQ ditampilkan dengan kategori untuk context
  - AI bisa reference FAQ saat generate jawaban
  - Format: Q&A dengan preview 300 karakter

- **Enhanced System Prompt**
  - Kombinasi: Peraturan + FAQ + AI Memory + Chat History + AI Guidelines
  - Feedback loop: AI improve dari jawaban yang digenerate
  - Context-aware: Snapshot peraturan dan FAQ saat generate
  - Learning over time: Semakin banyak data, semakin smart

#### Development Tools
- **Laravel Debugbar**
  - Install Laravel Debugbar untuk development debugging
  - Package: barryvdh/laravel-debugbar v4.0
  - Auto-enabled di environment development
  - Memudahkan debug query, request, response, dll

### 🐛 Bug Fixes

- **Fix Field Label Error**
  - Migration untuk make field `label` di tabel `pengaturan` nullable
  - Fix error: "Field 'label' doesn't have a default value"
  - Sekarang AI Guidelines bisa disimpan tanpa label

- **Fix SSL Certificate Error**
  - Disable SSL verification di development environment
  - Fix cURL error 60: SSL certificate problem
  - Production tetap pakai SSL verification
  - Solusi untuk Laragon/Windows local development

### 📦 Database Changes

- **Migration: update_pengaturan_table_make_label_nullable**
  - Alter tabel `pengaturan` field `label` jadi nullable
  - Backward compatible dengan data existing

- **Migration: buat_tabel_ai_memory**
  - Tabel baru untuk AI learning system
  - Index: kategori_terdeteksi, is_good_example, created_at
  - Foreign key ke users dengan onDelete set null
  - JSON fields untuk snapshot context

### 🔧 Technical Changes

- **Model: AiMemory**
  - Model baru dengan fillable semua fields
  - Cast: peraturan_used, faq_used as array
  - Scopes: goodExamples(), byKategori(), mostUsed()
  - Method: incrementUsage() untuk tracking

- **Service: LayananGroq**
  - Method baru: formatFaq() untuk format FAQ di prompt
  - Method baru: formatMemory() untuk format AI Memory di prompt
  - Method baru: saveToMemory() untuk simpan hasil generate
  - Enhanced buatSystemPrompt() dengan FAQ dan Memory
  - SSL verification conditional berdasarkan environment

- **Controller: DashboardController**
  - Auto save ke AI Memory setiap generate jawaban
  - Return memory_id di response JSON
  - Default is_good_example = true untuk semua generate

### 📝 Files Modified

- `app/Models/AiMemory.php` (new)
- `app/Services/LayananGroq.php` (enhanced)
- `app/Http/Controllers/DashboardController.php` (enhanced)
- `database/migrations/2026_02_17_175105_update_pengaturan_table_make_label_nullable.php` (new)
- `database/migrations/2026_02_17_175713_buat_tabel_ai_memory.php` (new)
- `composer.json` (debugbar added)

---

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

