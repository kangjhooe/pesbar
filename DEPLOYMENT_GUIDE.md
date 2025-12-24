# Panduan Deploy ke Hosting Tanpa npm install

## Masalah
Hosting tidak bisa menjalankan `npm install` karena:
- Node.js/npm tidak tersedia di hosting
- Memory limit terlalu kecil
- Timeout saat install dependencies

## Solusi: Build Assets di Local Sebelum Deploy

### Langkah 1: Build Assets di Local

Jalankan perintah berikut di komputer lokal Anda:

```bash
# Install dependencies (jika belum)
npm install

# Build untuk production
npm run build
```

Ini akan menghasilkan folder `public/build` yang berisi file CSS dan JS yang sudah di-compile.

### Langkah 2: Upload Folder Build ke Hosting

Ada 2 opsi:

#### Opsi A: Commit folder build ke Git (Recommended)

1. Edit `.gitignore` dan hapus atau comment baris `/public/build`
2. Commit folder build:
   ```bash
   git add public/build
   git commit -m "Add production build assets"
   git push origin main
   ```
3. Di hosting, pull dari git dan folder build akan ikut ter-upload

#### Opsi B: Upload Manual via FTP/cPanel

1. Setelah build di local, upload folder `public/build` ke hosting via FTP atau File Manager
2. Pastikan struktur folder sama: `public/build/`

### Langkah 3: Pastikan File .env Production Benar

Pastikan di hosting file `.env` sudah dikonfigurasi dengan benar:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Pastikan Vite menggunakan build assets
VITE_APP_NAME="${APP_NAME}"
```

### Langkah 4: Optimasi untuk Production

Di hosting, jalankan:

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternatif: Build Otomatis dengan Script

Buat file `deploy.sh` untuk memudahkan:

```bash
#!/bin/bash
echo "Building assets..."
npm run build
echo "Build complete!"
echo "Upload public/build folder to your hosting"
```

## Troubleshooting

### Error: Vite manifest not found
- Pastikan folder `public/build` sudah ter-upload
- Pastikan file `public/build/manifest.json` ada
- Clear cache Laravel: `php artisan cache:clear`

### Error: Assets tidak loading
- Cek `APP_URL` di `.env` sudah benar
- Pastikan folder `public/build` permissionnya 755
- Cek file `.htaccess` di folder public

### Build file terlalu besar
- Gunakan kompresi di hosting (gzip)
- Pastikan CDN untuk static assets (opsional)

## Catatan Penting

1. **Jangan commit node_modules** - tetap di .gitignore
2. **Commit public/build** - folder ini perlu di-commit untuk production
3. **Build setiap kali ada perubahan CSS/JS** - rebuild dan commit ulang
4. **Backup folder build** - simpan backup sebelum rebuild

## Workflow Development

1. Develop di local dengan `npm run dev` (hot reload)
2. Test di local dengan `npm run build`
3. Build untuk production: `npm run build`
4. Commit folder `public/build` ke git
5. Push ke GitHub
6. Pull di hosting

