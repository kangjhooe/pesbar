# Panduan Fitur Event Popup Dinamis

## Deskripsi
Fitur Event Popup Dinamis memungkinkan admin untuk membuat dan mengelola popup yang akan ditampilkan kepada pengunjung website sesuai dengan jadwal yang ditentukan.

## Fitur Utama

### 1. Manajemen Event Popup
- **CRUD Lengkap**: Create, Read, Update, Delete event popup
- **Toggle Status**: Aktifkan/nonaktifkan event popup
- **Jadwal Otomatis**: Popup muncul sesuai tanggal mulai dan selesai
- **Tampilan Sekali**: Popup hanya muncul sekali per user menggunakan localStorage

### 2. Admin Panel
- Akses melalui menu "Event Popup" di sidebar admin
- Tampilan tabel dengan informasi lengkap
- Form create/edit dengan validasi
- Toggle status dengan satu klik

### 3. Frontend Integration
- Popup otomatis muncul di halaman utama
- Responsive design dengan TailwindCSS
- Animasi fade-in yang smooth
- Dapat ditutup dengan tombol, klik luar, atau tombol Escape

## Struktur Database

### Tabel: event_popups
```sql
- id (bigint, primary key)
- title (string) - Judul event popup
- message (text) - Pesan yang ditampilkan
- start_date (date) - Tanggal mulai popup
- end_date (date) - Tanggal selesai popup
- status (boolean) - Status aktif/non-aktif
- created_at (timestamp)
- updated_at (timestamp)
```

## Cara Penggunaan

### 1. Membuat Event Popup Baru
1. Login sebagai admin
2. Klik menu "Event Popup" di sidebar
3. Klik tombol "Tambah Event Popup"
4. Isi form:
   - **Judul Event**: Judul yang akan ditampilkan
   - **Pesan**: Konten popup
   - **Tanggal Mulai**: Kapan popup mulai ditampilkan
   - **Tanggal Selesai**: Kapan popup berhenti ditampilkan
   - **Status**: Centang untuk mengaktifkan
5. Klik "Simpan"

### 2. Mengelola Event Popup
- **Edit**: Klik ikon edit untuk mengubah event popup
- **Toggle Status**: Klik ikon toggle untuk mengaktifkan/nonaktifkan
- **Hapus**: Klik ikon trash untuk menghapus (dengan konfirmasi)

### 3. Cara Kerja di Frontend
- Popup otomatis muncul jika:
  - Status = true (aktif)
  - Tanggal sekarang >= start_date
  - Tanggal sekarang <= end_date
  - User belum pernah melihat popup ini (localStorage)
- Popup muncul 1 detik setelah halaman dimuat
- User dapat menutup popup dengan:
  - Klik tombol "Tutup"
  - Klik di luar area popup
  - Tekan tombol Escape

## File yang Dibuat/Dimodifikasi

### Model & Migration
- `app/Models/EventPopup.php` - Model dengan scope dan method helper
- `database/migrations/2025_09_30_124909_create_event_popups_table.php` - Migration tabel

### Controller
- `app/Http/Controllers/EventPopupController.php` - CRUD controller dengan toggle status

### Routes
- `routes/web.php` - Route resource dan toggle status

### Views
- `resources/views/admin/event-popups/index.blade.php` - Daftar event popup
- `resources/views/admin/event-popups/create.blade.php` - Form tambah event popup
- `resources/views/admin/event-popups/edit.blade.php` - Form edit event popup
- `resources/views/admin/event-popups/show.blade.php` - Detail event popup
- `resources/views/layouts/admin-simple.blade.php` - Menu sidebar admin
- `resources/views/layouts/public.blade.php` - Popup modal dan JavaScript

### Seeder
- `database/seeders/EventPopupSeeder.php` - Data contoh

### Controller Modifikasi
- `app/Http/Controllers/HomeController.php` - Menambahkan data event popup

## Teknologi yang Digunakan

### Backend
- **Laravel**: Framework PHP
- **Eloquent ORM**: Database operations
- **Carbon**: Date manipulation
- **Validation**: Form validation

### Frontend
- **TailwindCSS**: Styling
- **Font Awesome**: Icons
- **Vanilla JavaScript**: Popup functionality
- **localStorage**: User tracking

## Keamanan

### Validasi
- Semua input divalidasi di controller
- Tanggal selesai harus >= tanggal mulai
- Required fields: title, message, start_date, end_date

### Authorization
- Hanya admin yang dapat mengakses manajemen event popup
- Route protected dengan middleware auth dan role:admin

## Performance

### Database
- Index pada kolom status, start_date, end_date
- Scope query untuk popup aktif
- Pagination pada list admin

### Frontend
- Popup hanya dimuat jika ada event aktif
- localStorage untuk menghindari popup berulang
- Animasi CSS untuk performa optimal

## Troubleshooting

### Popup Tidak Muncul
1. Cek status event popup (harus aktif)
2. Cek tanggal mulai dan selesai
3. Cek localStorage browser (hapus jika perlu)
4. Cek console browser untuk error JavaScript

### Error di Admin Panel
1. Cek permission user (harus admin)
2. Cek validasi form
3. Cek database connection

## Pengembangan Selanjutnya

### Fitur yang Bisa Ditambahkan
1. **Multiple Popup**: Support beberapa popup bersamaan
2. **Target Audience**: Popup berdasarkan user role
3. **Analytics**: Tracking view dan click popup
4. **Template**: Template popup yang dapat dipilih
5. **Scheduling**: Advanced scheduling dengan timezone
6. **A/B Testing**: Multiple variant popup

### Optimasi
1. **Caching**: Cache query popup aktif
2. **CDN**: Static assets untuk popup
3. **Lazy Loading**: Load popup content on demand
4. **Mobile Optimization**: Better mobile experience

## Support

Jika mengalami masalah dengan fitur Event Popup, silakan:
1. Cek log Laravel di `storage/logs/laravel.log`
2. Cek browser console untuk error JavaScript
3. Pastikan semua migration sudah dijalankan
4. Pastikan seeder sudah dijalankan untuk data contoh
