# Panduan Pull ke Hosting dari GitHub

## ✅ Status
Semua perubahan sudah ter-push ke GitHub:
- ✅ Build assets (`public/build/`)
- ✅ Deployment guide
- ✅ Deployment scripts
- ✅ Semua perubahan terbaru

Repository: `https://github.com/kangjhooe/pesbar.git`

---

## Cara Pull ke Hosting

Ada beberapa metode tergantung setup hosting Anda:

### Metode 1: Git Pull via SSH/Terminal (Recommended)

Jika hosting Anda sudah terhubung dengan git:

```bash
# Masuk ke folder project di hosting
cd /path/to/your/project

# Pull perubahan terbaru
git pull origin main

# Atau jika branch berbeda
git pull origin main --no-edit
```

**Jika ada konflik:**
```bash
# Backup dulu
cp -r . ../backup-$(date +%Y%m%d)

# Reset ke remote (hati-hati, ini akan overwrite perubahan lokal)
git fetch origin
git reset --hard origin/main
```

---

### Metode 2: Git Clone (Jika Belum Ada Git di Hosting)

Jika hosting belum ada git repository:

```bash
# Backup folder lama dulu (jika ada)
mv /path/to/old/project /path/to/old/project-backup

# Clone dari GitHub
git clone https://github.com/kangjhooe/pesbar.git /path/to/new/project

# Masuk ke folder
cd /path/to/new/project

# Copy file .env dari backup (jika ada)
cp /path/to/old/project-backup/.env .env

# Edit .env sesuai kebutuhan hosting
nano .env
```

---

### Metode 3: Download ZIP dari GitHub

Jika hosting tidak support git:

1. Buka: https://github.com/kangjhooe/pesbar
2. Klik tombol **"Code"** → **"Download ZIP"**
3. Upload dan extract di hosting
4. Pastikan folder `public/build/` ikut ter-upload

---

### Metode 4: Via cPanel File Manager

1. Login ke cPanel
2. Buka **File Manager**
3. Masuk ke folder project
4. Jika ada git, buka **Terminal** di cPanel dan jalankan `git pull origin main`
5. Jika tidak ada git, upload file ZIP dan extract

---

## Setelah Pull - Langkah Wajib

### 1. Update File .env

Pastikan file `.env` di hosting sudah benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_db
DB_PASSWORD=password_db

# Pastikan Vite menggunakan build assets
VITE_APP_NAME="${APP_NAME}"
```

### 2. Install Dependencies (jika belum)

```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Atau jika development
composer install
```

### 3. Run Migrations (jika ada migration baru)

```bash
php artisan migrate --force
```

### 4. Set Permissions

```bash
# Set permission untuk storage dan cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Atau jika user berbeda
chown -R youruser:youruser storage bootstrap/cache
```

### 5. Clear dan Cache

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Link Storage (jika perlu)

```bash
php artisan storage:link
```

---

## Verifikasi

Setelah pull, cek apakah semua berjalan:

1. **Cek folder build:**
   ```bash
   ls -la public/build/
   ```
   Harus ada file: `manifest.json`, `assets/app-*.css`, `assets/app-*.js`

2. **Cek website:**
   - Buka website di browser
   - Cek apakah CSS dan JS loading dengan benar
   - Cek console browser untuk error

3. **Cek log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## Troubleshooting

### Error: "Vite manifest not found"
```bash
# Pastikan folder build ada
ls -la public/build/

# Jika tidak ada, build di local dan upload manual
# Atau pull ulang dari GitHub
git pull origin main
```

### Error: "Permission denied"
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Error: "Class not found"
```bash
# Reinstall dependencies
composer dump-autoload
composer install --no-dev --optimize-autoloader
```

### Error: "Database connection failed"
- Cek file `.env` sudah benar
- Cek kredensial database
- Test koneksi database

### CSS/JS tidak loading
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan view:cache
```

---

## Script Otomatis Pull

Buat file `pull.sh` di hosting untuk memudahkan:

```bash
#!/bin/bash
echo "🔄 Pulling from GitHub..."
git pull origin main

echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "🔄 Running migrations..."
php artisan migrate --force

echo "🧹 Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "⚡ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Done!"
```

Jalankan dengan:
```bash
chmod +x pull.sh
./pull.sh
```

---

## Catatan Penting

1. **Backup dulu** sebelum pull (database dan files)
2. **Jangan pull di jam sibuk** (traffic tinggi)
3. **Test di staging** dulu jika memungkinkan
4. **Monitor log** setelah pull untuk error
5. **Keep .env secure** - jangan commit ke git

---

## Support

Jika ada masalah, cek:
- File log: `storage/logs/laravel.log`
- Error di browser console
- Server error log di hosting

