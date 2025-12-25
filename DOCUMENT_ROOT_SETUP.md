# Panduan Setup Document Root untuk Laravel

## ✅ Jawaban Singkat

**Document root HARUS diarahkan ke:**
```
pesbar/public
```

**BUKAN ke:**
```
pesbar
```

---

## 🎯 Mengapa Harus ke `public/`?

1. **Keamanan**: File sensitif (`.env`, `vendor/`, `app/`, dll) tidak bisa diakses langsung dari browser
2. **Entry Point**: File `public/index.php` adalah entry point Laravel
3. **Best Practice**: Standar Laravel untuk production

---

## 📋 Cara Setup Berdasarkan Jenis Hosting

### 1. cPanel / Shared Hosting

#### Metode A: Via cPanel Domain Settings

1. Login ke **cPanel**
2. Buka **"Addon Domains"** atau **"Subdomains"**
3. Edit domain yang ingin dikonfigurasi
4. Set **Document Root** ke:
   ```
   /home/username/pesbar/public
   ```
   atau
   ```
   /public_html/pesbar/public
   ```
5. Klik **"Change"** atau **"Save"**

#### Metode B: Via File Manager

1. Buka **File Manager** di cPanel
2. Masuk ke folder `pesbar`
3. Buka folder `public`
4. Copy semua file dari `public` ke root domain (jika perlu)
5. Atau lebih baik: Set document root langsung ke `pesbar/public`

#### Metode C: Via .htaccess di Root

Jika tidak bisa ubah document root, buat file `.htaccess` di root `pesbar/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Catatan**: Metode ini kurang aman, lebih baik ubah document root langsung.

---

### 2. VPS dengan Apache

Edit file virtual host:

```bash
sudo nano /etc/apache2/sites-available/pesbar.conf
```

Konfigurasi:

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    
    # INI YANG PENTING - Document Root ke public
    DocumentRoot /var/www/pesbar/public
    
    <Directory /var/www/pesbar/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/pesbar_error.log
    CustomLog ${APACHE_LOG_DIR}/pesbar_access.log combined
</VirtualHost>
```

Enable dan restart:

```bash
sudo a2ensite pesbar.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 3. VPS dengan Nginx

Edit file konfigurasi:

```bash
sudo nano /etc/nginx/sites-available/pesbar
```

Konfigurasi:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    
    # INI YANG PENTING - Root ke public
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

Enable dan restart:

```bash
sudo ln -s /etc/nginx/sites-available/pesbar /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

### 4. Cloud Hosting (DigitalOcean, AWS, dll)

Sesuaikan dengan platform yang digunakan, tapi prinsipnya sama:

**Apache:**
```
DocumentRoot /path/to/pesbar/public
```

**Nginx:**
```
root /path/to/pesbar/public;
```

---

## 🔍 Cara Verifikasi Document Root Benar

### 1. Cek File yang Bisa Diakses

Jika document root benar, Anda bisa akses:
- ✅ `https://yourdomain.com/` → Halaman utama
- ✅ `https://yourdomain.com/css/app.css` → File CSS (jika ada)
- ✅ `https://yourdomain.com/js/app.js` → File JS (jika ada)

Jika document root salah, akan muncul:
- ❌ Error 403 atau 404
- ❌ File `.env` bisa diakses (SANGAT BERBAHAYA!)
- ❌ Folder `vendor/` bisa diakses

### 2. Test dengan File PHP

Buat file `test.php` di folder `public/`:

```php
<?php
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'];
echo "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'];
```

Akses: `https://yourdomain.com/test.php`

Harus menampilkan:
```
Document Root: /path/to/pesbar/public
Script Name: /test.php
```

### 3. Cek Laravel Route

Akses route Laravel:
```
https://yourdomain.com/login
```

Jika muncul halaman login Laravel → **BENAR** ✅  
Jika muncul error 404 atau file listing → **SALAH** ❌

---

## ⚠️ Masalah Umum dan Solusi

### Masalah 1: "403 Forbidden"

**Penyebab**: Permission folder salah

**Solusi**:
```bash
chmod -R 755 /path/to/pesbar/public
chown -R www-data:www-data /path/to/pesbar/public
```

### Masalah 2: "404 Not Found" untuk semua route

**Penyebab**: Document root tidak ke `public/`

**Solusi**: Pastikan document root ke `pesbar/public`

### Masalah 3: File `.env` bisa diakses

**Penyebab**: Document root salah (ke `pesbar` bukan `pesbar/public`)

**Solusi**: **SEGERA** ubah document root ke `pesbar/public`!

### Masalah 4: CSS/JS tidak loading

**Penyebab**: Path asset salah atau document root salah

**Solusi**:
1. Pastikan document root ke `pesbar/public`
2. Cek file `.env`: `APP_URL=https://yourdomain.com`
3. Clear cache: `php artisan config:clear`

---

## 📁 Struktur Folder yang Benar

```
pesbar/
├── app/                    # ❌ TIDAK bisa diakses dari browser
├── bootstrap/              # ❌ TIDAK bisa diakses dari browser
├── config/                 # ❌ TIDAK bisa diakses dari browser
├── database/               # ❌ TIDAK bisa diakses dari browser
├── public/                 # ✅ INI document root
│   ├── index.php          # ✅ Entry point Laravel
│   ├── .htaccess          # ✅ Apache config
│   ├── build/             # ✅ Assets production
│   └── ...
├── resources/              # ❌ TIDAK bisa diakses dari browser
├── routes/                 # ❌ TIDAK bisa diakses dari browser
├── storage/                # ❌ TIDAK bisa diakses dari browser
├── vendor/                 # ❌ TIDAK bisa diakses dari browser
└── .env                    # ❌ TIDAK bisa diakses dari browser
```

---

## 🔒 Keamanan Tambahan

Setelah document root benar, tambahkan proteksi di `.htaccess` di root `pesbar/`:

```apache
# Block access to sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Block access to vendor, storage, etc
RedirectMatch 403 /vendor/.*
RedirectMatch 403 /storage/.*
RedirectMatch 403 /\.env
```

---

## 📝 Checklist Setup

- [ ] Document root diarahkan ke `pesbar/public`
- [ ] File `.htaccess` ada di folder `public/`
- [ ] Permission folder `public/` sudah benar (755)
- [ ] File `.env` tidak bisa diakses dari browser
- [ ] Route Laravel berfungsi dengan benar
- [ ] CSS/JS loading dengan benar
- [ ] SSL/HTTPS sudah dikonfigurasi (opsional tapi recommended)

---

## 🆘 Butuh Bantuan?

Jika masih ada masalah:
1. Cek error log: `storage/logs/laravel.log`
2. Cek server error log (Apache/Nginx)
3. Pastikan PHP version sesuai (8.2+)
4. Pastikan semua extension PHP terinstall


