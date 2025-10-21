# Data Login Aplikasi Pesisir Barat

Berikut adalah data login untuk berbagai role yang tersedia di aplikasi:

> **⚠️ PERHATIAN:** Email admin yang benar adalah `admin@pesisirbarat.id` (bukan `admin@pesbar.com`)

## 🔐 Data Login

### 1. Admin
- **Email:** `admin@pesisirbarat.id`
- **Password:** `password`
- **Role:** Admin
- **Status:** Terverifikasi
- **Akses:** Full access ke semua fitur admin

### 2. Editor
- **Email:** `editor@pesbar.com`
- **Password:** `password`
- **Role:** Editor
- **Status:** Terverifikasi
- **Akses:** Mengelola artikel dan konten

### 3. Penulis Terverifikasi
- **Email:** `penulis@pesbar.com`
- **Password:** `password`
- **Role:** Penulis
- **Status:** Terverifikasi
- **Akses:** Menulis dan mengelola artikel sendiri

### 4. Penulis Belum Terverifikasi
- **Email:** `penulis2@pesbar.com`
- **Password:** `password`
- **Role:** Penulis
- **Status:** Belum Terverifikasi
- **Akses:** Terbatas sampai verifikasi

### 5. User Biasa
- **Email:** `user@pesbar.com`
- **Password:** `password`
- **Role:** User
- **Status:** Belum Terverifikasi
- **Akses:** Hanya membaca konten

## 📝 Catatan Penting

- Semua password default adalah: `password`
- Untuk keamanan, disarankan untuk mengubah password setelah login pertama
- User dengan status "Belum Terverifikasi" memiliki akses terbatas
- Admin memiliki akses penuh ke semua fitur sistem

## 🚀 Cara Menjalankan Seeder

**PENTING:** Ada dua seeder yang berbeda untuk user:

1. **DatabaseSeeder** (sudah berjalan) - membuat admin dengan email `admin@pesisirbarat.id`
2. **AdminUserSeeder** - membuat user dengan email `admin@pesbar.com`

Untuk menjalankan seeder yang lengkap:

```bash
php artisan db:seed
```

Atau untuk menjalankan seeder admin tambahan:

```bash
php artisan db:seed --class=AdminUserSeeder
```

**Catatan:** Jika Anda ingin menggunakan data dari AdminUserSeeder, pastikan untuk menjalankan seeder tersebut terlebih dahulu.

## 🔧 URL Login

- **Login Admin/Editor:** `/admin/login`
- **Login Penulis:** `/penulis/login`
- **Login User:** `/login`

---
*Dokumen ini dibuat untuk keperluan development dan testing aplikasi Pesisir Barat*
