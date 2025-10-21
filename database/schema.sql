-- Database Schema untuk Portal Berita Kabupaten Pesisir Barat
-- Format tanggal: DD-MM-YYYY sesuai preferensi user

CREATE DATABASE IF NOT EXISTS pesisir_barat_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pesisir_barat_news;

-- Tabel Kategori Berita
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#3498db',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Users (Admin/Redaksi)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor', 'reporter') DEFAULT 'reporter',
    avatar VARCHAR(255),
    bio TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Berita
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    category_id INT,
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,
    is_breaking BOOLEAN DEFAULT FALSE,
    view_count INT DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Tags
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Article Tags (Many-to-Many)
CREATE TABLE article_tags (
    article_id INT,
    tag_id INT,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Tabel Komentar
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);

-- Tabel Newsletter Subscribers
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
);

-- Tabel Pengaturan Website
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Menu Navigasi
CREATE TABLE navigation_menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES navigation_menu(id) ON DELETE CASCADE
);

-- Tabel Widget
CREATE TABLE widgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    content TEXT,
    widget_type ENUM('text', 'html', 'weather', 'popular_news', 'categories', 'newsletter') DEFAULT 'text',
    position ENUM('sidebar', 'footer', 'header') DEFAULT 'sidebar',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Log Aktivitas
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert Data Awal Kategori
INSERT INTO categories (name, slug, description, icon, color) VALUES
('Politik', 'politik', 'Berita seputar politik dan pemerintahan', 'fas fa-landmark', '#e74c3c'),
('Ekonomi', 'ekonomi', 'Berita ekonomi dan bisnis', 'fas fa-chart-line', '#f39c12'),
('Sosial', 'sosial', 'Berita sosial dan kemasyarakatan', 'fas fa-users', '#27ae60'),
('Olahraga', 'olahraga', 'Berita olahraga', 'fas fa-futbol', '#3498db'),
('Teknologi', 'teknologi', 'Berita teknologi dan digital', 'fas fa-laptop', '#9b59b6'),
('Kesehatan', 'kesehatan', 'Berita kesehatan dan medis', 'fas fa-heartbeat', '#e67e22'),
('Pendidikan', 'pendidikan', 'Berita pendidikan dan akademik', 'fas fa-graduation-cap', '#1abc9c');

-- Insert Data Awal Menu Navigasi
INSERT INTO navigation_menu (title, url, sort_order) VALUES
('Beranda', '/', 1),
('Politik', '/kategori/politik', 2),
('Ekonomi', '/kategori/ekonomi', 3),
('Sosial', '/kategori/sosial', 4),
('Olahraga', '/kategori/olahraga', 5),
('Teknologi', '/kategori/teknologi', 6),
('Kesehatan', '/kategori/kesehatan', 7),
('Pendidikan', '/kategori/pendidikan', 8);

-- Insert Data Awal Pengaturan
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_title', 'Portal Berita Kabupaten Pesisir Barat', 'Judul website'),
('site_description', 'Portal berita resmi Kabupaten Pesisir Barat yang menyajikan informasi terkini, akurat, dan terpercaya untuk masyarakat.', 'Deskripsi website'),
('site_keywords', 'berita pesisir barat, kabupaten pesisir barat, lampung, berita lokal', 'Keywords SEO'),
('contact_email', 'info@pesisirbaratnews.id', 'Email kontak'),
('contact_phone', '+62 721 123456', 'Nomor telepon'),
('contact_address', 'Jl. Raya Pesisir Barat No. 1, Kec. Pesisir Barat, Kab. Pesisir Barat, Lampung', 'Alamat'),
('facebook_url', '#', 'URL Facebook'),
('twitter_url', '#', 'URL Twitter'),
('instagram_url', '#', 'URL Instagram'),
('youtube_url', '#', 'URL YouTube'),
('posts_per_page', '12', 'Jumlah berita per halaman'),
('enable_comments', '1', 'Aktifkan komentar'),
('enable_newsletter', '1', 'Aktifkan newsletter'),
('weather_api_key', '', 'API Key untuk widget cuaca');

-- Insert Data Awal Widget
INSERT INTO widgets (title, widget_type, position, sort_order, is_active) VALUES
('Cuaca Hari Ini', 'weather', 'sidebar', 1, TRUE),
('Berita Populer', 'popular_news', 'sidebar', 2, TRUE),
('Kategori Berita', 'categories', 'sidebar', 3, TRUE),
('Newsletter', 'newsletter', 'sidebar', 4, TRUE);

-- Insert Admin User (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@pesisirbaratnews.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert Sample Articles
INSERT INTO articles (title, slug, excerpt, content, category_id, author_id, status, is_featured, is_breaking, published_at) VALUES
('Bupati Pesisir Barat Resmikan Pembangunan Jembatan Penghubung Antar Desa', 'bupati-resmikan-jembatan-penghubung-antar-desa', 'Pembangunan jembatan sepanjang 150 meter ini diharapkan dapat meningkatkan konektivitas antar desa dan mempermudah akses transportasi masyarakat.', 'Pemerintah Kabupaten Pesisir Barat melalui Bupati telah meresmikan pembangunan jembatan penghubung antar desa yang akan menjadi solusi transportasi bagi masyarakat. Jembatan sepanjang 150 meter ini dibangun dengan anggaran sebesar 2,5 miliar rupiah dan diharapkan dapat meningkatkan konektivitas antar desa di wilayah Pesisir Barat.

Pembangunan jembatan ini merupakan bagian dari program pembangunan infrastruktur yang menjadi prioritas pemerintah daerah. Dengan adanya jembatan ini, masyarakat dapat dengan mudah mengakses berbagai layanan publik dan pusat ekonomi di wilayah tersebut.

"Pembangunan jembatan ini merupakan wujud komitmen kami untuk meningkatkan kesejahteraan masyarakat melalui pembangunan infrastruktur yang berkualitas," ujar Bupati dalam sambutannya.

Proyek ini diharapkan selesai dalam waktu 8 bulan dan akan memberikan dampak positif bagi perekonomian masyarakat sekitar.', 1, 1, 'published', TRUE, TRUE, NOW()),
('UMKM Pesisir Barat Raih Penghargaan Nasional', 'umkm-pesisir-barat-raih-penghargaan-nasional', 'Tiga UMKM dari Kabupaten Pesisir Barat berhasil meraih penghargaan dalam ajang kompetisi nasional untuk kategori inovasi produk.', 'Tiga Usaha Mikro, Kecil, dan Menengah (UMKM) dari Kabupaten Pesisir Barat berhasil mengharumkan nama daerah dengan meraih penghargaan dalam ajang kompetisi nasional untuk kategori inovasi produk. Prestasi ini menunjukkan bahwa UMKM di Pesisir Barat memiliki potensi yang besar untuk bersaing di tingkat nasional.

Ketiga UMKM yang meraih penghargaan tersebut adalah:
1. CV Sari Laut - penghargaan untuk produk olahan ikan terbaik
2. UD Kerajinan Bambu - penghargaan untuk produk kerajinan inovatif
3. CV Kopi Pesisir - penghargaan untuk produk kopi berkualitas premium

Pemerintah daerah memberikan apresiasi tinggi atas prestasi yang diraih oleh ketiga UMKM tersebut. "Ini adalah bukti bahwa UMKM di Pesisir Barat memiliki kualitas yang tidak kalah dengan daerah lain," ujar Kepala Dinas Koperasi dan UKM.

Penghargaan ini diharapkan dapat memotivasi UMKM lain untuk terus berinovasi dan meningkatkan kualitas produknya.', 2, 1, 'published', FALSE, FALSE, NOW()),
('Sekolah Dasar Terbaik di Pesisir Barat', 'sekolah-dasar-terbaik-di-pesisir-barat', 'SD Negeri 1 Pesisir Barat meraih predikat sekolah terbaik tingkat provinsi dengan prestasi akademik dan non-akademik yang membanggakan.', 'SD Negeri 1 Pesisir Barat berhasil meraih predikat sebagai sekolah dasar terbaik tingkat provinsi Lampung. Prestasi ini diraih berkat pencapaian akademik dan non-akademik yang membanggakan selama tahun ajaran 2024.

Sekolah yang berlokasi di pusat kota Pesisir Barat ini berhasil mengungguli 1.200 sekolah dasar lainnya di seluruh provinsi Lampung. Prestasi yang diraih meliputi:

1. Nilai rata-rata Ujian Nasional tertinggi se-provinsi
2. Juara 1 lomba cerdas cermat tingkat provinsi
3. Juara 1 lomba paduan suara tingkat provinsi
4. Juara 2 lomba olahraga tingkat provinsi

Kepala Sekolah SD Negeri 1 Pesisir Barat mengungkapkan bahwa prestasi ini adalah hasil kerja keras seluruh warga sekolah, mulai dari guru, siswa, hingga orang tua murid. "Kami berkomitmen untuk terus meningkatkan kualitas pendidikan dan memberikan yang terbaik untuk anak-anak," ujarnya.

Pemerintah daerah memberikan apresiasi khusus atas prestasi yang diraih sekolah ini dan berharap dapat menjadi inspirasi bagi sekolah-sekolah lain di Pesisir Barat.', 7, 1, 'published', FALSE, FALSE, NOW());

-- Insert Sample Tags
INSERT INTO tags (name, slug) VALUES
('Pembangunan', 'pembangunan'),
('Infrastruktur', 'infrastruktur'),
('UMKM', 'umkm'),
('Pendidikan', 'pendidikan'),
('Prestasi', 'prestasi'),
('Pemerintah', 'pemerintah'),
('Masyarakat', 'masyarakat'),
('Ekonomi', 'ekonomi');

-- Insert Article Tags
INSERT INTO article_tags (article_id, tag_id) VALUES
(1, 1), (1, 2), (1, 6), (1, 7),
(2, 3), (2, 8), (2, 5),
(3, 4), (3, 5), (3, 7);
