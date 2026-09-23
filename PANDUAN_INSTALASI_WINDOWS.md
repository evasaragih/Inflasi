# 💻 Panduan Instalasi & Pengoperasian Aplikasi di PC Windows

Aplikasi **Dashboard Inflasi & Harga Pangan Kota Padang** dirancang agar **sangat mudah dipindahkan dan dijalankan di komputer Windows lain** menggunakan basis data portable **SQLite** (tanpa perlu setting server MySQL yang rumit).

---

## 🚀 CARA CEPAT (2 LANGKAH SAJA)

### 📁 Langkah 1: Copy Folder Aplikasi
Salin seluruh folder aplikasi `inflasi` dari PC ini ke PC Windows tujuan (misal menggunakan Flashdisk, Google Drive, atau Jaringan LAN). Simpan di folder apa saja, contoh:
- `D:\inflasi`
- `C:\Apps\inflasi`

---

### ⚙️ Langkah 2: Jalankan Instalasi Pertama
Buka folder aplikasi di PC Windows tersebut, lalu:
1. Dobel-klik file **`INSTAL_PERTAMA.bat`**.
2. Tunggu hingga proses otomatis selesai (membuat database, memasang tabel, dan seeding data 2024-2026).
3. Setelah muncul pesan **"INSTALASI SUKSES 100%"**, tutup jendela tersebut.

---

### 🌐 Langkah 3: Jalankan Aplikasi Kapan Saja
Setiap kali Anda ingin membuka dan menggunakan aplikasi website:
1. Dobel-klik file **`JALANKAN_WEB.bat`**.
2. Web Browser (Chrome/Edge/Firefox) akan **otomatis terbuka** mengarah ke:
   - **Halaman Utama Web**: [http://localhost:8000](http://localhost:8000)
   - **Panel Admin**: [http://localhost:8000/admin](http://localhost:8000/admin)

---

## 🔑 KREDENSIAL LOGIN ADMIN

| Field | Nilai |
| :--- | :--- |
| **URL Login** | `http://localhost:8000/admin` |
| **Email Admin** | `admin@padang.go.id` |
| **Password** | `admin123` |

---

## 🛠️ PERSYARATAN SYARAT MINIMAL PC WINDOWS

PC Windows tujuan hanya membutuhkan **PHP 8.2 atau lebih baru** (dengan ekstensi `pdo_sqlite`, `mbstring`, `fileinfo` yang umum aktif).

### Jika PC Windows Belum Memiliki PHP:
Anda memiliki 3 pilihan cepat:
1. **Menggunakan Laragon / XAMPP**: Jika PC sudah memiliki Laragon atau XAMPP, pastikan PHP aktif.
2. **Download Portable PHP 8.2+ (Tanpa Install)**:
   - Download dari [windows.php.net/download](https://windows.php.net/download/) (pilih *VS16 x64 Non Thread Safe* / *Thread Safe* zip).
   - Ekstrak ke `C:\php`.
   - Tambahkan `C:\php` ke *Environment Variables PATH* Windows.
3. **Menggunakan Installer PHP Windows**:
   - Jalankan installer PHP 8.2+ pada Windows.

---

## 🎯 DAFTAR FILE OTOMATISASI YANG DISEDIAKAN

- 📄 **`INSTAL_PERTAMA.bat`** : Menyiapkan database SQLite, skema tabel, dan seeder data 2024-2026.
- 📄 **`JALANKAN_WEB.bat`** : Menjalankan server lokal & otomatis membuka browser.
- 📄 **`database/database.sqlite`** : File database portable berisi seluruh data inflasi, andil, harga pangan, dan stok.
