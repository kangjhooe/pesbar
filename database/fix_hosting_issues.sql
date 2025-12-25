-- ============================================
-- Script untuk memperbaiki masalah di hosting
-- ============================================
-- 
-- Masalah yang diperbaiki:
-- 1. Tabel sessions tidak ada
-- 2. User admin tidak ada di database
--
-- Cara penggunaan:
-- 1. Buka phpMyAdmin di hosting
-- 2. Pilih database aplikasi Anda
-- 3. Klik tab "SQL"
-- 4. Copy-paste script ini dan jalankan
-- ============================================

-- 1. Buat tabel sessions (jika belum ada)
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

-- 2. Buat user admin (jika belum ada)
-- Email: admin@pesisirbarat.id
-- Password: password
INSERT INTO `users` (`name`, `email`, `password`, `role`, `verified`, `email_verified_at`, `created_at`, `updated_at`)
VALUES (
    'Administrator',
    'admin@pesisirbarat.id',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'admin',
    1,
    NOW(),
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `verified` = VALUES(`verified`);

-- Catatan:
-- - Email admin: admin@pesisirbarat.id
-- - Password: password
-- - Jika user sudah ada, password akan diupdate ke password default


