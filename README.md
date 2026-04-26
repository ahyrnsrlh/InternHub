# InternHub

InternHub adalah aplikasi monitoring program magang berbasis Laravel untuk mengelola presensi, lokasi, logbook harian, dan pelaporan antara Admin dan Peserta Magang.

Dokumen ini disiapkan agar tim client bisa melakukan instalasi awal, menjalankan aplikasi, dan memahami alur penggunaan tanpa kebingungan.

## 1. Gambaran Singkat

### Tujuan Sistem

- Memudahkan admin memantau kehadiran dan aktivitas magang.
- Memusatkan data operasional program magang dalam satu dashboard.
- Menyediakan alur kerja peserta magang: presensi, logbook, dan laporan.

### Role Utama

- Admin
  : akses dashboard admin, data intern, data kehadiran, lokasi, laporan, dan pengaturan profil admin.
- Intern atau User
  : akses dashboard peserta, presensi, lokasi, map, logbook, laporan, dan profil peserta.

## 2. Fitur Utama

### Fitur Admin

- Dashboard statistik kehadiran dan validasi.
- Grafik tren dan aktivitas peserta.
- Manajemen data peserta magang.
- Monitoring data kehadiran dan status validasi lokasi.
- Manajemen lokasi magang.
- Rekap laporan kehadiran.
- Halaman profil admin khusus di URL internhub/admin/profile.

### Fitur Peserta

- Dashboard personal.
- Presensi check-in dan check-out.
- Monitoring lokasi dan map.
- Pengisian logbook harian.
- Laporan dan ekspor PDF.
- Profil peserta.

## 3. Teknologi yang Digunakan

- Backend: Laravel 13
- Bahasa: PHP 8.3+
- Frontend tooling: Vite, Tailwind CSS, Alpine.js
- Database: MySQL atau SQLite
- PDF Export: barryvdh/laravel-dompdf
- Auth starter: Laravel Breeze

## 4. Kebutuhan Sistem

Pastikan environment sudah terpasang:

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js 20+ dan npm
- Database server (MySQL direkomendasikan untuk lingkungan tim)
- Git

Untuk Windows + Laragon:

- Aktifkan service Apache atau Nginx dan MySQL.
- Pastikan versi PHP Laragon sesuai (8.3+).

## 5. Instalasi Awal

### A. Clone dan Install Dependensi

```bash
git clone <url-repository> InternHub
cd InternHub

composer install
npm install
```

### B. Siapkan Environment

```bash
copy .env.example .env
php artisan key:generate
```

Atur koneksi database di file .env sesuai server lokal Anda.

Contoh MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=internhub
DB_USERNAME=root
DB_PASSWORD=
```

### C. Migrasi dan Seeder

```bash
php artisan migrate --seed
```

Perintah di atas akan membuat tabel dan data awal untuk akun demo.

### D. Jalankan Aplikasi

Opsi 1, satu command untuk development stack:

```bash
composer run dev
```

Opsi 2, jalankan terpisah:

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Terminal 3 (opsional, jika butuh proses queue):

```bash
php artisan queue:listen --tries=1 --timeout=0
```

## 6. Akun Demo Seeder

Setelah menjalankan migrate --seed, akun berikut tersedia:

- Admin
  : email admin@internhub.test
  : password password

- Mentor
  : email mentor@internhub.test
  : password password

- Intern
  : email alex@internhub.test
  : password password

- Intern
  : email sarah@internhub.test
  : password password

## 7. URL Penting

- Login: /login
- Dashboard admin: /internhub/admin/dashboard
- Profil admin: /internhub/admin/profile
- Dashboard peserta: /user/dashboard

## 8. Konfigurasi Penting

File konfigurasi khusus project:

- config/internhub.php

Konfigurasi tracking yang bisa diatur lewat .env:

```env
TRACKING_START_HOUR=8
TRACKING_END_HOUR=18
TRACKING_INTERVAL_SECONDS=120
```

## 9. Struktur Folder Inti

- app/Http/Controllers
  : logika endpoint admin dan user.
- app/Models
  : model domain seperti User, Attendance, DailyLog, Location.
- resources/views/pages/admin
  : halaman admin.
- resources/views/pages/user
  : halaman peserta.
- routes/web.php
  : routing utama web.
- database/migrations
  : skema database.
- database/seeders
  : data awal aplikasi.

## 10. Perintah Operasional Harian

### Bersihkan Cache Aplikasi

```bash
php artisan optimize:clear
```

### Menjalankan Test

```bash
php artisan test
```

### Menampilkan Daftar Route

```bash
php artisan route:list
```

## 11. Panduan Deploy Singkat

Checklist minimum production:

1. Set APP_ENV=production dan APP_DEBUG=false di .env.
2. Set APP_URL sesuai domain production.
3. Jalankan composer install dengan opsi production dependency.
4. Jalankan php artisan migrate --force.
5. Build asset frontend dengan npm run build.
6. Jalankan php artisan optimize.
7. Konfigurasikan web server document root ke folder public.
8. Pastikan permission folder storage dan bootstrap/cache benar.

## 12. Troubleshooting Umum

### Route tidak ditemukan

Gejala: error route not defined.

Solusi:

```bash
php artisan optimize:clear
php artisan route:list
```

### Halaman berubah tapi browser masih menampilkan tampilan lama

Gejala: view seolah belum update.

Solusi:

- Hard refresh browser (Ctrl + F5).
- Jalankan php artisan optimize:clear.

### 419 Page Expired

Gejala: submit form gagal dengan status 419.

Solusi:

- Pastikan token CSRF tersedia.
- Pastikan domain dan APP_URL konsisten.
- Hapus cookie session lama, login ulang.

### Asset CSS atau JS tidak termuat

Gejala: tampilan berantakan atau script tidak jalan.

Solusi:

```bash
npm install
npm run dev
```

## 13. Catatan Keamanan

- Jangan gunakan kredensial demo di production.
- Ganti seluruh password awal setelah deploy.
- Batasi akses akun admin dan gunakan password policy kuat.

## 14. Lisensi

Project ini menggunakan basis Laravel Framework yang berlisensi MIT.
