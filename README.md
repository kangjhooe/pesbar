# Portal Berita Kabupaten Pesisir Barat

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

Portal berita resmi Kabupaten Pesisir Barat yang dibangun dengan Laravel. Aplikasi ini menyajikan informasi terkini, akurat, dan terpercaya untuk masyarakat dengan sistem manajemen konten yang lengkap.

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Deploy ke Hosting](#-deploy-ke-hosting)
- [Struktur Proyek](#-struktur-proyek)
- [Teknologi yang Digunakan](#-teknologi-yang-digunakan)
- [Lisensi](#-lisensi)

## 🚀 Fitur Utama

### Frontend
- ✅ **Desain Responsif** - Mobile-friendly dengan UI modern menggunakan Tailwind CSS
- ✅ **Berita Terkini** - Menampilkan berita terbaru, trending, dan populer
- ✅ **Kategori Berita** - Politik, Ekonomi, Sosial, Olahraga, Teknologi, Kesehatan, Pendidikan
- ✅ **Pencarian Lanjutan** - Fitur pencarian dengan filter dan autocomplete
- ✅ **Detail Artikel** - Halaman detail dengan komentar dan berita terkait
- ✅ **Sistem Komentar** - Komentar dengan sistem moderasi
- ✅ **Newsletter** - Sistem berlangganan newsletter
- ✅ **Widget Cuaca** - Informasi cuaca terkini
- ✅ **Widget Waktu Sholat** - Informasi waktu sholat
- ✅ **Polling** - Sistem polling interaktif
- ✅ **Event Popup** - Popup event yang dapat dikonfigurasi
- ✅ **SEO Friendly** - Optimasi untuk mesin pencari
- ✅ **Sitemap** - Sitemap XML untuk SEO

### Backend & Admin Panel
- ✅ **Sistem Autentikasi** - Login dengan Laravel Breeze dan OAuth Google
- ✅ **Role Management** - User, Penulis, Editor, Admin dengan hak akses berbeda
- ✅ **Dashboard Admin** - Statistik dan overview sistem
- ✅ **Manajemen Artikel** - CRUD artikel dengan status (draft/pending_review/published/rejected)
- ✅ **Sistem Review Artikel** - Penulis unverified harus melalui review editor
- ✅ **Manajemen Kategori** - Kelola kategori berita
- ✅ **Manajemen Tag** - Sistem tagging artikel
- ✅ **Manajemen Komentar** - Approve/reject komentar
- ✅ **Manajemen User** - Kelola pengguna dan upgrade role
- ✅ **Newsletter Management** - Kelola subscriber newsletter
- ✅ **Pengaturan Website** - Konfigurasi situs lengkap
- ✅ **Activity Log** - Log aktivitas admin
- ✅ **Image Processing** - Optimasi gambar otomatis dengan multiple sizes dan WebP
- ✅ **Email Notifications** - Notifikasi email untuk berbagai event

### Teknologi & Performa
- ✅ **Redis Integration** - Caching, Session, dan Queue dengan Redis
- ✅ **Image Optimization** - Multiple sizes dan konversi WebP
- ✅ **Advanced Search** - Full-text search dengan filters
- ✅ **Queue System** - Background job processing
- ✅ **Caching System** - Cache untuk performa optimal

## 💻 Persyaratan Sistem

### Minimum Requirements
- **PHP**: 8.2 atau lebih baru
- **Composer**: 2.x
- **Node.js**: 18.x atau lebih baru
- **NPM**: 9.x atau lebih baru
- **Database**: MySQL 5.7+ / MariaDB 10.3+ / PostgreSQL 13+
- **Web Server**: Apache 2.4+ / Nginx 1.18+

### PHP Extensions
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PDO_MySQL (atau PDO_PGSQL untuk PostgreSQL)
- Tokenizer
- XML

### Opsional (Recommended)
- **Redis**: 6.0+ (untuk caching dan queue)
- **ImageMagick** atau **GD**: (untuk image processing)

## 🛠️ Instalasi

### 1. Clone Repository

```bash
git clone [repository-url]
cd pesbar
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Setup Environment

```bash
# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_database
DB_PASSWORD=password_database
```

### 5. Setup Database

```bash
# Jalankan migration
php artisan migrate

# Jalankan seeder (opsional)
php artisan db:seed
```

### 6. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Setup Storage Link

```bash
php artisan storage:link
```

### 8. Jalankan Aplikasi

```bash
# Development server
php artisan serve

# Atau gunakan script composer
composer dev
```

Aplikasi akan berjalan di `http://localhost:8000`

## ⚙️ Konfigurasi

### Konfigurasi Email

Edit file `.env` untuk konfigurasi email:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Konfigurasi Redis (Opsional)

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
REDIS_CLIENT=phpredis
```

### Konfigurasi Google OAuth

```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

### Konfigurasi Queue Worker

Untuk production, jalankan queue worker:

```bash
php artisan queue:work --tries=3
```

Atau gunakan supervisor untuk menjalankan queue worker secara otomatis.

## 🚀 Deploy ke Hosting

### Deploy ke Shared Hosting (cPanel)

#### 1. Persiapan File

```bash
# Build assets untuk production
npm run build

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 2. Upload File ke Server

- Upload semua file ke server melalui FTP/cPanel File Manager
- **Jangan upload** folder berikut:
  - `node_modules/`
  - `.git/`
  - `tests/`
  - `.env.example`
  - `README.md` (atau file dokumentasi lainnya)

#### 3. Konfigurasi di Server

1. **Buat file `.env`** di root direktori dengan konfigurasi production:
```env
APP_NAME="Portal Berita Pesisir Barat"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_database
DB_PASSWORD=password_database

# ... konfigurasi lainnya
```

2. **Set permissions** untuk folder storage dan bootstrap/cache:
```bash
chmod -R 775 storage bootstrap/cache
```

3. **Jalankan migration** (jika belum):
```bash
php artisan migrate --force
```

4. **Buat symbolic link** untuk storage:
```bash
php artisan storage:link
```

#### 4. Konfigurasi Web Server

**Untuk Apache**, pastikan file `.htaccess` ada di folder `public/`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Untuk Nginx**, konfigurasi di server block:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/pesbar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### 5. Set Document Root

Pastikan document root mengarah ke folder `public/`:
- **Apache**: Set di cPanel atau `.htaccess`
- **Nginx**: Set di konfigurasi server block

### Deploy ke VPS (Ubuntu/Debian)

#### 1. Persiapan Server

```bash
# Update sistem
sudo apt update && sudo apt upgrade -y

# Install PHP dan extensions
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js dan NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install nginx -y

# Install MySQL
sudo apt install mysql-server -y
```

#### 2. Clone dan Setup Aplikasi

```bash
# Clone repository
cd /var/www
sudo git clone [repository-url] pesbar
cd pesbar

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env dengan konfigurasi production
nano .env
```

#### 3. Setup Database

```bash
# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE pesbar_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pesbar_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON pesbar_db.* TO 'pesbar_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Jalankan migration
php artisan migrate --force

# Jalankan seeder (opsional)
php artisan db:seed --force
```

#### 4. Setup Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/pesbar

# Set permissions
sudo chmod -R 755 /var/www/pesbar
sudo chmod -R 775 /var/www/pesbar/storage
sudo chmod -R 775 /var/www/pesbar/bootstrap/cache

# Buat storage link
php artisan storage:link
```

#### 5. Konfigurasi Nginx

```bash
sudo nano /etc/nginx/sites-available/pesbar
```

Tambahkan konfigurasi:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/pesbar/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/pesbar /etc/nginx/sites-enabled/

# Test konfigurasi
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

#### 6. Setup SSL dengan Let's Encrypt

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal sudah otomatis setup
```

#### 7. Setup Queue Worker dengan Supervisor

```bash
# Install Supervisor
sudo apt install supervisor -y

# Buat konfigurasi
sudo nano /etc/supervisor/conf.d/pesbar-worker.conf
```

Tambahkan:

```ini
[program:pesbar-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pesbar/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/pesbar/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start pesbar-worker:*
```

#### 8. Setup Cron Job

```bash
# Edit crontab
sudo crontab -e -u www-data
```

Tambahkan:

```cron
* * * * * cd /var/www/pesbar && php artisan schedule:run >> /dev/null 2>&1
```

#### 9. Optimize untuk Production

```bash
cd /var/www/pesbar

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --no-dev --optimize-autoloader
```

### Checklist Deploy

- [ ] File `.env` sudah dikonfigurasi dengan benar
- [ ] `APP_DEBUG=false` untuk production
- [ ] Database sudah dibuat dan dikonfigurasi
- [ ] Migration sudah dijalankan
- [ ] Storage link sudah dibuat
- [ ] Permissions folder storage dan bootstrap/cache sudah benar (775)
- [ ] Assets sudah di-build (`npm run build`)
- [ ] Document root mengarah ke folder `public/`
- [ ] Queue worker sudah berjalan (jika menggunakan queue)
- [ ] Cron job sudah di-setup (untuk scheduled tasks)
- [ ] SSL certificate sudah di-setup (untuk HTTPS)
- [ ] Backup database sudah dilakukan

## 📁 Struktur Proyek

```
pesbar/
├── app/                    # Aplikasi utama
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Exception handlers
│   ├── Helpers/           # Helper classes
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Jobs/              # Queue jobs
│   ├── Models/            # Eloquent models
│   ├── Notifications/     # Email notifications
│   ├── Observers/         # Model observers
│   ├── Policies/          # Authorization policies
│   ├── Providers/         # Service providers
│   └── Services/          # Business logic services
├── bootstrap/             # Bootstrap files
├── config/                # Konfigurasi aplikasi
├── database/              # Migrations, seeders, factories
├── public/                # Public assets (document root)
├── resources/             # Views, CSS, JS
├── routes/                # Route definitions
├── storage/               # Logs, cache, uploaded files
├── tests/                 # Test files
└── vendor/                # Composer dependencies
```

## 🛠️ Teknologi yang Digunakan

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL/MariaDB/PostgreSQL
- **Frontend**: Tailwind CSS, Alpine.js, Vite
- **Caching**: Redis (opsional)
- **Queue**: Redis/Database
- **Image Processing**: Intervention Image
- **Authentication**: Laravel Breeze, Laravel Socialite
- **Email**: Laravel Mail

## 📝 Default Login

Setelah menjalankan seeder, Anda dapat login dengan:

**Admin:**
- Email: `admin@example.com`
- Password: `password`

**Penulis:**
- Email: `penulis@example.com`
- Password: `password`

> ⚠️ **PENTING**: Segera ubah password default setelah pertama kali login!

## 🔒 Keamanan

- Pastikan `APP_DEBUG=false` di production
- Gunakan HTTPS untuk semua koneksi
- Jaga kerahasiaan file `.env`
- Update dependencies secara berkala
- Gunakan password yang kuat untuk database
- Setup firewall untuk membatasi akses

## 📚 Dokumentasi Tambahan

- [Panduan Autentikasi](AUTHENTICATION_GUIDE.md)
- [Panduan Event Popup](EVENT_POPUP_GUIDE.md)
- [Panduan Widget](WIDGET_IMPLEMENTATION.md)
- [Panduan Redis](REDIS_SETUP.md)
- [Fitur Implementasi](FEATURES_IMPLEMENTATION.md)

## 🤝 Kontribusi

Kontribusi sangat diterima! Silakan buat issue atau pull request.

## 📄 Lisensi

Aplikasi ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).

---

**Dibuat dengan ❤️ menggunakan Laravel**
