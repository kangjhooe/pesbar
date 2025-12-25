-- ============================================
-- SCRIPT FIX MIGRATION: make_username_required
-- Untuk mengatasi error: Duplicate key name 'users_username_unique'
-- ============================================
-- 
-- CARA PENGGUNAAN:
-- 1. Buka phpMyAdmin di hosting
-- 2. Pilih database aplikasi Anda
-- 3. Klik tab "SQL"
-- 4. Copy-paste script ini
-- 5. Klik "Go" atau "Jalankan"
--
-- ============================================

-- 1. Pastikan semua user punya username
-- Cek dulu user yang belum punya username
SELECT id, name, email, username 
FROM users 
WHERE username IS NULL OR username = '';

-- Generate username untuk user yang belum punya
UPDATE users 
SET username = CONCAT('user-', id) 
WHERE username IS NULL OR username = '';

-- 2. Fix duplikat username (jika ada)
-- Cek apakah ada username duplikat
SELECT username, COUNT(*) as count, GROUP_CONCAT(id) as user_ids
FROM users 
WHERE username IS NOT NULL AND username != ''
GROUP BY username 
HAVING count > 1;

-- Jika ada duplikat, update manual (uncomment dan sesuaikan ID):
-- UPDATE users SET username = CONCAT(username, '-', id) WHERE id = [ID_YANG_DUPLIKAT];

-- 3. Pastikan index unique ada (jika belum ada)
-- Cek apakah index sudah ada
SHOW INDEX FROM users WHERE Key_name = 'users_username_unique';

-- Jika belum ada, buat index unique
-- (Hanya jalankan jika index belum ada!)
-- ALTER TABLE users ADD UNIQUE KEY users_username_unique (username);

-- 4. Ubah kolom menjadi NOT NULL
-- Pastikan semua user sudah punya username sebelum menjalankan ini!
ALTER TABLE users 
MODIFY COLUMN username VARCHAR(255) NOT NULL;

-- 5. Tandai migration sebagai selesai
-- Cek apakah migration sudah tercatat
SELECT * FROM migrations 
WHERE migration = '2025_12_25_082043_make_username_required_in_users_table';

-- Jika belum ada, insert manual
INSERT INTO migrations (migration, batch) 
SELECT '2025_12_25_082043_make_username_required_in_users_table', 
       COALESCE(MAX(batch), 0) + 1 
FROM migrations
WHERE NOT EXISTS (
    SELECT 1 FROM migrations 
    WHERE migration = '2025_12_25_082043_make_username_required_in_users_table'
);

-- ============================================
-- VERIFIKASI
-- ============================================
-- Setelah selesai, cek hasilnya:

-- Cek apakah semua user punya username
SELECT COUNT(*) as users_without_username 
FROM users 
WHERE username IS NULL OR username = '';
-- Harus return: 0

-- Cek apakah kolom sudah NOT NULL
SHOW COLUMNS FROM users WHERE Field = 'username';
-- Harus tampil: Null = NO

-- Cek apakah index unique sudah ada
SHOW INDEX FROM users WHERE Key_name = 'users_username_unique';
-- Harus ada 1 row

-- Cek apakah migration sudah tercatat
SELECT * FROM migrations 
WHERE migration = '2025_12_25_082043_make_username_required_in_users_table';
-- Harus ada 1 row

