# Portal Berita Kabupaten Pesisir Barat

Portal berita resmi Kabupaten Pesisir Barat yang menyajikan informasi terkini, akurat, dan terpercaya untuk masyarakat.

## 🚀 Fitur Utama

### Frontend
- ✅ **Desain Responsif** - Mobile-friendly dengan UI modern
- ✅ **Berita Terkini** - Menampilkan berita terbaru dan trending
- ✅ **Kategori Berita** - Politik, Ekonomi, Sosial, Olahraga, Teknologi, Kesehatan, Pendidikan
- ✅ **Pencarian Berita** - Fitur pencarian dengan pagination
- ✅ **Detail Artikel** - Halaman detail dengan komentar dan berita terkait
- ✅ **Newsletter** - Sistem berlangganan newsletter
- ✅ **Widget Cuaca** - Informasi cuaca terkini
- ✅ **Social Media Integration** - Link ke media sosial

### Backend & Admin Panel
- ✅ **Sistem Autentikasi** - Login admin dengan role management
- ✅ **Dashboard Admin** - Statistik dan overview sistem
- ✅ **Manajemen Artikel** - CRUD artikel dengan status (draft/published)
- ✅ **Manajemen Kategori** - Kelola kategori berita
- ✅ **Manajemen Komentar** - Approve/reject komentar
- ✅ **Manajemen User** - Kelola pengguna admin
- ✅ **Newsletter Management** - Kelola subscriber newsletter
- ✅ **Pengaturan Website** - Konfigurasi situs
- ✅ **Activity Log** - Log aktivitas admin

### Database & Teknologi
- ✅ **MySQL Database** - Struktur database lengkap
- ✅ **PHP 7.4+** - Backend dengan PDO
- ✅ **Responsive CSS** - Styling modern dengan Flexbox/Grid
- ✅ **JavaScript** - Interaktivitas frontend
- ✅ **Security** - Input sanitization dan prepared statements

## 📋 Persyaratan Sistem

- **Web Server**: Apache/Nginx
- **PHP**: 7.4 atau lebih baru
- **MySQL**: 5.7 atau lebih baru
- **Extensions**: PDO, PDO_MySQL, mbstring, fileinfo

## 🛠️ Instalasi

### 1. Clone/Download Project
```bash
# Jika menggunakan Git
git clone [repository-url]
cd pesbar

# Atau download dan extract ke folder htdocs
```

### 2. Setup Database
1. Buat database MySQL baru:
```sql
CREATE DATABASE pesisir_barat_news CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import struktur database:
```bash
mysql -u root -p pesisir_barat_news < database/schema.sql
```

### 3. Konfigurasi Database
Edit file `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'pesisir_barat_news';
private $username = 'root';
private $password = 'your_password';
```

### 4. Setup Permissions
```bash
# Buat folder uploads dan set permissions
mkdir uploads
chmod 755 uploads
chmod 644 config/database.php
```

### 5. Akses Website
- **Frontend**: `http://localhost/pesbar/`
- **Admin Panel**: `http://localhost/pesbar/admin/`

### 6. Login Admin
- **Username**: `admin`
- **Password**: `admin123`

## 📁 Struktur Project

```
pesbar/
├── admin/                  # Panel Admin
│   ├── login.php          # Halaman login
│   ├── dashboard.php      # Dashboard admin
│   ├── articles.php       # Manajemen artikel
│   ├── categories.php     # Manajemen kategori
│   ├── comments.php       # Manajemen komentar
│   ├── users.php          # Manajemen user
│   ├── newsletter.php     # Manajemen newsletter
│   └── settings.php       # Pengaturan website
├── assets/                # Assets Frontend
│   ├── css/
│   │   └── style.css      # Stylesheet utama
│   ├── js/
│   │   └── script.js      # JavaScript utama
│   └── images/            # Gambar website
├── berita/                # Halaman detail berita
│   └── detail.php         # Template detail artikel
├── config/                # Konfigurasi
│   └── database.php       # Koneksi database
├── database/              # Database
│   └── schema.sql         # Struktur database
├── includes/              # File PHP umum
│   └── functions.php      # Fungsi-fungsi helper
├── uploads/               # Upload file
├── index.php              # Halaman utama
├── search.php             # Halaman pencarian
├── newsletter-subscribe.php # Handler newsletter
└── README.md              # Dokumentasi
```

## 🎨 Customization

### Mengubah Tema
Edit file `assets/css/style.css` untuk mengubah:
- Warna utama
- Font
- Layout
- Responsive breakpoints

### Menambah Kategori
1. Login ke admin panel
2. Masuk ke menu "Kategori"
3. Tambah kategori baru
4. Atau edit langsung di database

### Mengubah Pengaturan
1. Login ke admin panel
2. Masuk ke menu "Pengaturan"
3. Ubah konfigurasi sesuai kebutuhan

## 🔧 Konfigurasi Lanjutan

### Email Configuration
Untuk fitur newsletter, konfigurasi SMTP di `includes/functions.php`:
```php
// Tambahkan konfigurasi email SMTP
```

### SEO Optimization
- Edit meta tags di setiap halaman
- Konfigurasi sitemap.xml
- Setup Google Analytics
- Optimasi gambar

### Security
- Ubah password admin default
- Setup SSL certificate
- Konfigurasi firewall
- Regular backup database

## 📊 Database Schema

### Tabel Utama
- `articles` - Data artikel berita
- `categories` - Kategori berita
- `users` - Data admin/redaksi
- `comments` - Komentar artikel
- `newsletter_subscribers` - Subscriber newsletter
- `settings` - Pengaturan website
- `navigation_menu` - Menu navigasi
- `widgets` - Widget sidebar
- `activity_logs` - Log aktivitas

## 🚀 Deployment

### Production Setup
1. **Server Requirements**:
   - PHP 7.4+
   - MySQL 5.7+
   - Apache/Nginx
   - SSL Certificate

2. **Security**:
   - Ubah password default
   - Setup firewall
   - Enable HTTPS
   - Regular updates

3. **Performance**:
   - Enable caching
   - Optimize images
   - Minify CSS/JS
   - Database indexing

### Backup
```bash
# Backup database
mysqldump -u root -p pesisir_barat_news > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d).tar.gz pesbar/
```

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## 📝 Changelog

### v1.0.0 (2024-12-15)
- ✅ Initial release
- ✅ Frontend responsive design
- ✅ Admin panel lengkap
- ✅ Database schema
- ✅ Authentication system
- ✅ Newsletter system
- ✅ Search functionality
- ✅ Comment system

## 📞 Support

Untuk bantuan dan support:
- **Email**: info@pesisirbaratnews.id
- **Phone**: +62 721 123456
- **Address**: Jl. Raya Pesisir Barat No. 1, Kec. Pesisir Barat, Kab. Pesisir Barat, Lampung

## 📄 License

© 2024 Portal Berita Kabupaten Pesisir Barat. All rights reserved.

---

**Dibuat dengan ❤️ untuk masyarakat Pesisir Barat**