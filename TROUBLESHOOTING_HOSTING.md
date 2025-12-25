# Troubleshooting Masalah di Hosting

## 🔴 Masalah: Admin Tidak Bisa Login

### Penyebab Umum:
1. **Tabel `sessions` belum dibuat** - Laravel menggunakan database untuk menyimpan session
2. **User admin belum ada di database** - Seeder belum dijalankan
3. **Password tidak cocok** - Hash password tidak sesuai

---

## ✅ Solusi Lengkap

### Opsi 1: Jalankan Migrasi dan Seeder (Disarankan)

Jika hosting Anda menyediakan akses SSH atau terminal:

```bash
# Jalankan migrasi
php artisan migrate --force

# Jalankan seeder untuk membuat admin
php artisan db:seed --class=DatabaseSeeder
```

**Data Login Admin:**
- Email: `admin@pesisirbarat.id`
- Password: `password`

---

### Opsi 2: Buat Manual via SQL (Jika Tidak Ada SSH)

Jika tidak bisa akses SSH, gunakan file SQL yang sudah disediakan:

**File:** `database/fix_hosting_issues.sql`

**Cara penggunaan:**
1. Buka **phpMyAdmin** di hosting
2. Pilih database aplikasi Anda (contoh: `u7699491_pesbar`)
3. Klik tab **"SQL"**
4. Copy-paste isi file `database/fix_hosting_issues.sql`
5. Klik **"Go"** atau **"Jalankan"**

Script SQL akan:
- ✅ Membuat tabel `sessions` (jika belum ada)
- ✅ Membuat user admin dengan email `admin@pesisirbarat.id` dan password `password`

---

### Opsi 3: Buat Tabel Sessions dan Admin Secara Terpisah

Jika ingin membuat secara manual satu per satu:

#### 1. Buat Tabel Sessions

```sql
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### 2. Buat User Admin

```sql
INSERT INTO `users` (`name`, `email`, `password`, `role`, `verified`, `email_verified_at`, `created_at`, `updated_at`)
VALUES (
    'Administrator',
    'admin@pesisirbarat.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    1,
    NOW(),
    NOW(),
    NOW()
);
```

**Catatan:** Password hash di atas adalah untuk password: `password`

---

## 🔍 Verifikasi Setelah Perbaikan

### 1. Cek Tabel Sessions
```sql
SHOW TABLES LIKE 'sessions';
```

### 2. Cek User Admin
```sql
SELECT id, name, email, role, verified FROM users WHERE email = 'admin@pesisirbarat.id';
```

### 3. Coba Login
- Buka: `https://yourdomain.com/login`
- Email: `admin@pesisirbarat.id`
- Password: `password`

---

## 🛠️ Masalah Lain yang Mungkin Terjadi

### Error: "Table 'sessions' doesn't exist"
**Solusi:** Jalankan script SQL untuk membuat tabel sessions (lihat Opsi 2 atau 3 di atas)

### Error: "These credentials do not match our records"
**Kemungkinan:**
- User admin belum dibuat → Jalankan seeder atau SQL untuk membuat admin
- Password salah → Pastikan menggunakan password: `password`
- Email salah → Pastikan menggunakan: `admin@pesisirbarat.id`

### Error: "Session store not set on request"
**Solusi:** Pastikan tabel `sessions` sudah dibuat dan konfigurasi `.env` benar:
```env
SESSION_DRIVER=database
```

### Login berhasil tapi langsung logout
**Kemungkinan:**
- Masalah dengan session/cookie di hosting
- Cek permission folder `storage/framework/sessions`
- Coba ubah `SESSION_DRIVER` ke `file` di `.env`:
```env
SESSION_DRIVER=file
```
Kemudian pastikan folder `storage/framework/sessions` memiliki permission 775

---

## 📝 Catatan Penting

1. **Email Admin yang Benar:**
   - ✅ `admin@pesisirbarat.id` (dari DatabaseSeeder)
   - ❌ `admin@pesbar.com` (dari AdminUserSeeder - hanya untuk development)

2. **Password Default:**
   - Semua user default menggunakan password: `password`
   - **PENTING:** Segera ubah password setelah login pertama!

3. **Keamanan:**
   - Setelah berhasil login, segera ubah password admin
   - Pastikan `APP_DEBUG=false` di production
   - Gunakan HTTPS untuk semua koneksi

---

## 🆘 Masih Bermasalah?

Jika masih mengalami masalah setelah mencoba semua solusi di atas:

1. **Cek Log Error:**
   - File: `storage/logs/laravel.log`
   - Atau cek error di hosting panel

2. **Cek Konfigurasi Database:**
   - Pastikan koneksi database di `.env` benar
   - Test koneksi database di phpMyAdmin

3. **Cek Permission Folder:**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

4. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

---

**File SQL Lengkap:** `database/fix_hosting_issues.sql`


