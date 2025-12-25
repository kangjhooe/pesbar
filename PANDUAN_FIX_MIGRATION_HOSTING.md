# 🔧 Panduan Memperbaiki Error Migration di Hosting

## ❌ Masalah yang Terjadi

Error saat menjalankan `php artisan migrate --force` di hosting:

```
SQLSTATE[42000]: Syntax error or access violation: 1061 
Duplicate key name 'users_username_unique'
```

**Penyebab:** Migration mencoba membuat unique index yang sudah ada di database.

---

## ✅ Solusi Lengkap

### **Opsi 1: Perbaiki Migration (Disarankan)**

Migration sudah diperbaiki di file `database/migrations/2025_12_25_082043_make_username_required_in_users_table.php`.

**Langkah-langkah:**

1. **Pull/Upload file migration yang sudah diperbaiki** ke hosting
2. **Jalankan migration lagi:**

```bash
php artisan migrate --force
```

---

### **Opsi 2: Fix Manual via SQL (Jika Migration Masih Gagal)**

Jika migration masih gagal, gunakan SQL langsung:

#### **Langkah 1: Cek Status Index di Database**

Login ke **phpMyAdmin** atau MySQL client, lalu jalankan:

```sql
SHOW INDEX FROM users WHERE Key_name = 'users_username_unique';
```

#### **Langkah 2: Pastikan Semua User Punya Username**

```sql
-- Cek user yang belum punya username
SELECT id, name, email, username FROM users WHERE username IS NULL OR username = '';

-- Jika ada, generate username untuk mereka
-- (Jalankan query ini untuk setiap user yang belum punya username)
UPDATE users 
SET username = CONCAT('user-', id) 
WHERE username IS NULL OR username = '';
```

#### **Langkah 3: Pastikan Username Unique**

```sql
-- Cek duplikat username
SELECT username, COUNT(*) as count 
FROM users 
WHERE username IS NOT NULL 
GROUP BY username 
HAVING count > 1;

-- Jika ada duplikat, perbaiki manual
-- Contoh: UPDATE users SET username = CONCAT(username, '-', id) WHERE id = [ID_YANG_DUPLIKAT];
```

#### **Langkah 4: Ubah Kolom Username Menjadi Required**

```sql
-- Pastikan index unique sudah ada
-- Jika belum ada, buat dulu:
-- ALTER TABLE users ADD UNIQUE KEY users_username_unique (username);

-- Ubah kolom menjadi NOT NULL (jika index sudah ada, jangan buat lagi)
ALTER TABLE users 
MODIFY COLUMN username VARCHAR(255) NOT NULL;
```

#### **Langkah 5: Mark Migration sebagai Selesai**

Setelah fix manual, tandai migration sebagai sudah dijalankan:

```sql
-- Cek apakah migration sudah tercatat
SELECT * FROM migrations WHERE migration = '2025_12_25_082043_make_username_required_in_users_table';

-- Jika belum ada, insert manual
INSERT INTO migrations (migration, batch) 
VALUES ('2025_12_25_082043_make_username_required_in_users_table', 
        (SELECT COALESCE(MAX(batch), 0) + 1 FROM migrations));
```

---

### **Opsi 3: Rollback & Migrate Ulang (Hati-hati!)**

⚠️ **PERINGATAN:** Hanya gunakan jika tidak ada data penting atau sudah backup!

```bash
# Rollback migration terakhir
php artisan migrate:rollback --step=1

# Atau rollback semua migration (SANGAT BERBAHAYA!)
# php artisan migrate:reset

# Jalankan migration lagi
php artisan migrate --force
```

---

## 🔍 Troubleshooting

### **Error: "Table doesn't exist"**

```bash
# Pastikan semua migration sebelumnya sudah dijalankan
php artisan migrate:status

# Jalankan migration dari awal (jika perlu)
php artisan migrate --force
```

### **Error: "Column already exists"**

```bash
# Cek status migration
php artisan migrate:status

# Jika migration sudah dijalankan sebagian, rollback dulu
php artisan migrate:rollback --step=1
php artisan migrate --force
```

### **Error: "Access denied" atau Permission Error**

```bash
# Pastikan user database punya hak akses penuh
# Cek di cPanel atau hubungi support hosting

# Atau coba dengan user root (jika development)
mysql -u root -p
GRANT ALL PRIVILEGES ON nama_database.* TO 'username_db'@'localhost';
FLUSH PRIVILEGES;
```

### **Error: "Class not found" atau "Model not found"**

```bash
# Clear cache dan autoload
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

---

## 📋 Checklist Sebelum Migrate di Hosting

- [ ] **Backup database** terlebih dahulu
- [ ] **Backup file** aplikasi (jika perlu)
- [ ] Pastikan file `.env` sudah benar
- [ ] Pastikan koneksi database berfungsi
- [ ] Cek `php artisan migrate:status` untuk melihat migration yang belum dijalankan
- [ ] Pastikan semua file migration sudah ter-upload
- [ ] Pastikan PHP version sesuai (8.2+)
- [ ] Pastikan extension MySQL/PDO sudah aktif

---

## 🚀 Script SQL Lengkap untuk Fix Manual

Jika semua cara di atas gagal, gunakan script SQL ini:

```sql
-- ============================================
-- SCRIPT FIX MIGRATION: make_username_required
-- ============================================

-- 1. Pastikan semua user punya username
UPDATE users 
SET username = CONCAT('user-', id) 
WHERE username IS NULL OR username = '';

-- 2. Fix duplikat username (jika ada)
-- Cek dulu:
SELECT username, COUNT(*) as count 
FROM users 
WHERE username IS NOT NULL 
GROUP BY username 
HAVING count > 1;

-- Jika ada duplikat, update manual:
-- UPDATE users SET username = CONCAT(username, '-', id) WHERE id = [ID];

-- 3. Pastikan index unique ada
-- Cek apakah index sudah ada
SHOW INDEX FROM users WHERE Key_name = 'users_username_unique';

-- Jika belum ada, buat index:
-- ALTER TABLE users ADD UNIQUE KEY users_username_unique (username);

-- 4. Ubah kolom menjadi NOT NULL
ALTER TABLE users 
MODIFY COLUMN username VARCHAR(255) NOT NULL;

-- 5. Tandai migration sebagai selesai
INSERT INTO migrations (migration, batch) 
SELECT '2025_12_25_082043_make_username_required_in_users_table', 
       COALESCE(MAX(batch), 0) + 1 
FROM migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations 
    WHERE migration = '2025_12_25_082043_make_username_required_in_users_table'
);
```

**Cara menggunakan:**
1. Buka **phpMyAdmin** di hosting
2. Pilih database aplikasi Anda
3. Klik tab **"SQL"**
4. Copy-paste script di atas
5. Klik **"Go"** atau **"Jalankan"**

---

## 📞 Langkah Terakhir

Setelah fix berhasil, verifikasi:

```bash
# Cek status migration
php artisan migrate:status

# Test aplikasi
# Buka website dan coba login
```

---

## ⚠️ Catatan Penting

1. **Selalu backup database** sebelum menjalankan migration atau fix manual
2. **Test di development** dulu sebelum deploy ke production
3. **Monitor log** setelah fix: `tail -f storage/logs/laravel.log`
4. **Jika masih error**, cek error log hosting atau hubungi support

---

**Dibuat untuk mengatasi error migration di hosting** 🚀


