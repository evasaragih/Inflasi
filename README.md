# Dashboard Inflasi Kota Padang

Website dashboard inflasi Kota Padang berbasis **Laravel 11** dan **Tailwind CSS**, dibangun dari data Excel yang dikirim (`Dashboard_Inflasi_Kota_Padang.xlsx`).

## Fitur

- **Ringkasan** — angka inflasi YoY terkini Kota Padang, perbandingan cepat dengan Sumbar & Nasional, tren grafik, penyumbang inflasi tertinggi, dan pergerakan harga pangan.
- **Data Inflasi** — tabel & grafik MTM / YTD / YoY untuk Nasional, Sumatera Barat, dan Kota Padang (Januari–Juli 2026).
- **Andil Inflasi** — kontribusi tiap kelompok pengeluaran terhadap inflasi headline, bisa difilter per bulan.
- **Harga Pangan Strategis** — riwayat harga mingguan 10 komoditas pokok dibanding HET, bisa dipilih per komoditas.

Desain memakai identitas visual khas Minangkabau (motif atap gonjong, palet warna songket: marun-emas-sand) dengan Chart.js untuk grafik interaktif. Tailwind CSS dimuat lewat CDN (tanpa perlu npm install/build Vite), jadi bebas dari masalah Vite/IPv6 di Laragon.

## Cara Menjalankan di Windows (Sangat Mudah!)

### **Metode 1: Menggunakan Skrip Otomatis (Rekomendasi)**
1. Copy folder project `inflasi` ke PC Windows Anda.
2. Klik 2x pada file **`setup_windows.bat`** (skrip akan otomatis membuat file `.env`, menginstall composer, me-link storage, dan menyiapkan database).
3. Klik 2x pada file **`jalankan_windows.bat`** untuk membuka browser dan menjalankan aplikasi di `http://localhost:8000`.

### **Metode 2: Manual via Command Prompt / Terminal**
1. Copy project ke folder tujuan (misal `C:\laragon\www\inflasi` atau `C:\xampp\htdocs\inflasi`).
2. Jalankan perintah berikut di CMD:
   ```cmd
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan storage:link
   php artisan migrate --seed
   php artisan serve
   ```
3. Buka **http://localhost:8000** di browser.

## Struktur Data

Data sumber Excel diekstrak menjadi 3 dataset yang disimpan di database/seeders/data/*.json, lalu diisi ke database lewat seeder:

- inflasi_indikator.json -> tabel inflasi_indikators (Nasional / Sumbar / Padang per bulan)
- andil_inflasi.json -> tabel andil_inflasis (12 kelompok pengeluaran per bulan)
- harga_pangan.json -> tabel harga_pangans (10 komoditas, mingguan)

Kalau nanti ada data bulan baru (Agustus dst), tinggal tambahkan barisnya di file JSON terkait lalu jalankan ulang php artisan migrate:fresh --seed.

## Struktur Project (ringkas)

app/Http/Controllers/   -> DashboardController, InflasiController, AndilController, PanganController
app/Models/              -> InflasiIndikator, AndilInflasi, HargaPangan
app/helpers.php          -> fungsi format angka gaya Indonesia (fnum, fsign)
database/migrations/     -> 3 migration tabel data inflasi
database/seeders/        -> 3 seeder + folder data/ berisi JSON hasil ekstraksi Excel
resources/views/         -> layouts/app.blade.php + dashboard, inflasi, andil, pangan
routes/web.php           -> 4 route halaman

## Panel Admin (Login, CRUD, & CMS)

Website ini sudah dilengkapi panel admin untuk mengelola semua data dan tampilan situs tanpa perlu edit kode.

### Login

Buka `/login` (atau klik "Masuk Admin" di footer situs). Akun default setelah `php artisan migrate --seed`:

- **Email:** admin@inflasipadang.test
- **Kata sandi:** admin123

**Segera ganti kata sandi ini** setelah login pertama kali (lewat Tinker: `php artisan tinker` lalu `User::first()->update(['password' => Hash::make('sandi-baru-anda')])`).

### Fitur Panel Admin (`/admin`)

- **Data Inflasi** — tambah, ubah, hapus data inflasi Nasional/Sumbar/Padang per bulan (IHK, MTM, YTD, YoY).
- **Andil Inflasi** — tambah, ubah, hapus andil tiap kelompok pengeluaran per bulan.
- **Harga Pangan** — tambah, ubah, hapus harga mingguan tiap komoditas. Perubahan (Rp) dan persentase dihitung otomatis dari harga minggu lalu & harga sekarang.
- **Pengaturan Situs** — ganti logo, nama situs, sub-judul, teks deskripsi tiap halaman, dan teks footer. Semua langsung tampil di halaman publik.

### Aktifkan Upload Logo (penting!)

Supaya upload logo berfungsi, jalankan sekali setelah `composer install`:

```
php artisan storage:link
```

Perintah ini membuat symlink dari `public/storage` ke `storage/app/public` tempat file logo disimpan.

## Ringkasan Alur Instalasi Penuh

```
composer config audit.block-insecure false
composer install
copy .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
php artisan serve
```
