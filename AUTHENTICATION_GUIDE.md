# Panduan Sistem Autentikasi dan Role Management

## Overview
Sistem autentikasi dan role management telah berhasil ditambahkan ke aplikasi Portal Berita Laravel dengan fitur-fitur lengkap sesuai spesifikasi.

## Fitur yang Telah Diimplementasikan

### 1. Registrasi & Login User
- ✅ **Laravel Breeze** untuk autentikasi standar (email + password)
- ✅ **Google OAuth** menggunakan Laravel Socialite
- ✅ Tabel users dengan field tambahan:
  - `role` ENUM('user','penulis','editor','admin') DEFAULT 'user'
  - `verified` BOOLEAN DEFAULT false
  - `provider` ENUM('local','google') DEFAULT 'local'
  - `provider_id` (nullable, untuk Google user id)

### 2. Role & Hak Akses
- ✅ **user** → bisa komentar, like artikel
- ✅ **penulis (unverified)** → bisa CRUD artikel sendiri, status artikel pending_review
- ✅ **penulis (verified)** → bisa publish langsung artikelnya
- ✅ **editor** → bisa review & approve artikel penulis
- ✅ **admin** → full access (verifikasi penulis, moderasi komentar, manajemen user)

### 3. Upgrade Role dari User ke Penulis
- ✅ Tabel `user_profiles` dengan field:
  - id, user_id, bio, avatar, social_links, website, location
- ✅ Form upgrade request untuk user dengan role 'user'
- ✅ Admin bisa melihat daftar user yang mengajukan upgrade
- ✅ Admin bisa mengubah role user ke 'penulis' dan menentukan status verified

### 4. Dashboard
- ✅ **Dashboard Penulis**:
  - CRUD artikel miliknya
  - Statistik artikel (views, komentar)
  - Status artikel (pending_review, published)
- ✅ **Dashboard Admin**:
  - Daftar user dengan aksi upgrade role dan toggle verified
  - Daftar artikel menunggu review
  - Statistik sistem

### 5. Profil Publik Penulis
- ✅ URL: `/penulis/{username}`
- ✅ Tampilkan nama, bio, avatar, badge "Penulis Terverifikasi ✅"
- ✅ Daftar artikel milik penulis

### 6. Middleware & Access Control
- ✅ Route dibatasi sesuai role (admin, editor, penulis, user)
- ✅ Penulis unverified: artikel harus pending_review
- ✅ Penulis verified: artikel bisa publish langsung
- ✅ Policy untuk Article (penulis hanya bisa edit artikel miliknya)

## Konfigurasi

### 1. Environment Variables
Tambahkan ke file `.env`:
```env
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### 2. Google OAuth Setup
1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project yang ada
3. Aktifkan Google+ API
4. Buat OAuth 2.0 credentials
5. Tambahkan authorized redirect URI: `http://localhost:8000/auth/google/callback`
6. Copy Client ID dan Client Secret ke file `.env`

## User Accounts Default

Setelah menjalankan seeder, tersedia akun default:

| Role | Email | Password | Status |
|------|-------|----------|--------|
| Admin | admin@pesbar.com | password | Terverifikasi |
| Editor | editor@pesbar.com | password | Terverifikasi |
| Penulis | penulis@pesbar.com | password | Terverifikasi |
| Penulis | penulis2@pesbar.com | password | Belum Terverifikasi |
| User | user@pesbar.com | password | Belum Terverifikasi |

## Routes

### Public Routes
- `/` - Home page
- `/penulis/{username}` - Profil publik penulis
- `/auth/google` - Google OAuth redirect
- `/auth/google/callback` - Google OAuth callback

### User Routes
- `/upgrade-request` - Form upgrade ke penulis
- `/dashboard` - Dashboard (redirect berdasarkan role)

### Penulis Routes (middleware: auth, role:penulis)
- `/penulis/dashboard` - Dashboard penulis
- `/penulis/profile` - Profil penulis
- `/penulis/articles/create` - Buat artikel
- `/penulis/articles/{article}/edit` - Edit artikel
- `/penulis/articles/{article}` - Delete artikel

### Admin Routes (middleware: auth, role:admin)
- `/admin/dashboard` - Dashboard admin
- `/admin/users` - Kelola users
- `/admin/users/{user}/upgrade` - Upgrade user ke penulis
- `/admin/users/{user}/toggle-verified` - Toggle verifikasi
- `/admin/articles/pending` - Artikel pending review
- `/admin/articles/{article}/approve` - Setujui artikel
- `/admin/articles/{article}/reject` - Tolak artikel

### Editor Routes (middleware: auth, role:editor)
- `/admin/dashboard` - Dashboard admin (sama dengan admin)
- `/admin/articles/pending` - Artikel pending review
- `/admin/articles/{article}/approve` - Setujui artikel
- `/admin/articles/{article}/reject` - Tolak artikel

## Database Schema

### Tabel `users` (updated)
```sql
- id (bigint, primary key)
- name (varchar)
- email (varchar, unique)
- email_verified_at (timestamp, nullable)
- password (varchar)
- role (enum: 'user','penulis','editor','admin', default: 'user')
- verified (boolean, default: false)
- provider (enum: 'local','google', default: 'local')
- provider_id (varchar, nullable)
- remember_token (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabel `user_profiles` (new)
```sql
- id (bigint, primary key)
- user_id (bigint, foreign key)
- bio (text, nullable)
- avatar (varchar, nullable)
- social_links (json, nullable)
- website (varchar, nullable)
- location (varchar, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabel `articles` (updated)
```sql
- id (bigint, primary key)
- title (varchar)
- slug (varchar, unique)
- excerpt (text, nullable)
- content (longtext)
- featured_image (varchar, nullable)
- category_id (bigint, foreign key, nullable)
- author_id (bigint, foreign key, nullable)
- status (enum: 'draft','pending_review','published','archived', default: 'draft')
- is_featured (boolean, default: false)
- is_breaking (boolean, default: false)
- view_count (unsigned integer, default: 0)
- views (unsigned integer, default: 0)
- published_at (timestamp, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Cara Penggunaan

### 1. Login sebagai Admin
1. Buka `/login`
2. Login dengan `admin@pesbar.com` / `password`
3. Akan redirect ke `/admin/dashboard`

### 2. Kelola Users
1. Dari dashboard admin, klik "Kelola Users"
2. Lihat daftar semua users
3. Upgrade user ke penulis dengan tombol "Upgrade ke Penulis"
4. Toggle verifikasi penulis dengan tombol "Verifikasi" / "Batalkan Verifikasi"

### 3. Review Artikel
1. Dari dashboard admin, klik "Review Artikel"
2. Lihat artikel yang menunggu review
3. Klik "Preview" untuk melihat artikel
4. Klik "Setujui" atau "Tolak"

### 4. User Upgrade ke Penulis
1. Login sebagai user biasa (`user@pesbar.com`)
2. Buka `/upgrade-request`
3. Isi form profil (bio, avatar, social links, dll)
4. Submit permintaan
5. Admin akan menerima notifikasi dan bisa approve

### 5. Penulis Membuat Artikel
1. Login sebagai penulis (`penulis@pesbar.com`)
2. Akan redirect ke `/penulis/dashboard`
3. Klik "Buat Artikel Baru"
4. Isi form artikel
5. Jika penulis terverifikasi: artikel langsung published
6. Jika penulis belum terverifikasi: artikel status pending_review

### 6. Login dengan Google
1. Buka `/login` atau `/register`
2. Klik "Login dengan Google" atau "Daftar dengan Google"
3. Akan redirect ke Google OAuth
4. Setelah authorize, akan kembali ke aplikasi dan auto-login

## Security Features

1. **Role-based Access Control**: Setiap route dilindungi middleware sesuai role
2. **Article Policy**: Penulis hanya bisa edit/delete artikel miliknya
3. **CSRF Protection**: Semua form dilindungi CSRF token
4. **Password Hashing**: Password di-hash menggunakan bcrypt
5. **Input Validation**: Semua input divalidasi dengan Laravel validation rules

## Troubleshooting

### 1. Google OAuth Error
- Pastikan Google Client ID dan Secret sudah benar
- Pastikan redirect URI sudah ditambahkan di Google Console
- Pastikan Google+ API sudah diaktifkan

### 2. Permission Denied
- Pastikan user sudah login
- Pastikan user memiliki role yang tepat
- Cek middleware di routes

### 3. Database Error
- Pastikan migration sudah dijalankan: `php artisan migrate`
- Pastikan seeder sudah dijalankan: `php artisan db:seed --class=AdminUserSeeder`

## Next Steps

1. **Email Verification**: Tambahkan email verification untuk registrasi
2. **Notification System**: Tambahkan notifikasi untuk admin saat ada artikel pending
3. **Comment System**: Implementasi sistem komentar dengan approval
4. **File Upload**: Implementasi upload gambar untuk artikel dan avatar
5. **Search & Filter**: Tambahkan fitur pencarian dan filter di dashboard
6. **Analytics**: Tambahkan analytics untuk tracking views dan engagement

## Support

Jika ada pertanyaan atau masalah, silakan buat issue di repository atau hubungi developer.
