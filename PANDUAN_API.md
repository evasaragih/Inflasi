# PANDUAN API — Dashboard Inflasi Kota Padang

Versi API: **v1**
Base URL (lokal): `http://127.0.0.1:8000/api/v1`
Base URL (server): `http://172.16.3.126:8222/api/v1`

Semua respons berbentuk JSON dengan pola yang sama:

```json
{
  "success": true,
  "message": "Daftar indikator inflasi",
  "meta": { "current_page": 1, "per_page": 25, "total": 36, "last_page": 2 },
  "data": []
}
```

Kalau gagal:

```json
{
  "success": false,
  "message": "Data yang dikirim tidak valid.",
  "errors": { "tahun": ["The tahun field is required."] }
}
```

---

## 1. Daftar File yang Ditambahkan

| File | Fungsi |
|---|---|
| `routes/api.php` | Definisi semua endpoint |
| `bootstrap/app.php` | **Diubah** — mendaftarkan routes/api.php, alias middleware, error JSON |
| `config/inflasi_api.php` | Konfigurasi API key, paginasi, daftar wilayah |
| `config/cors.php` | Pengaturan CORS untuk jalur `api/*` |
| `app/Traits/ApiResponse.php` | Format respons seragam |
| `app/Http/Middleware/ApiKeyMiddleware.php` | Pemeriksaan header `X-API-KEY` |
| `app/Http/Controllers/Api/BaseApiController.php` | Helper filter, urutan, paginasi |
| `app/Http/Controllers/Api/InflasiApiController.php` | Indikator inflasi |
| `app/Http/Controllers/Api/AndilApiController.php` | Andil per kelompok |
| `app/Http/Controllers/Api/HargaPanganApiController.php` | Harga pangan mingguan |
| `app/Http/Controllers/Api/StokPanganApiController.php` | Stok & kebutuhan pangan |
| `app/Http/Controllers/Api/RingkasanApiController.php` | Ringkasan dashboard + metadata |
| `.env` | **Diubah** — tambahan `INFLASI_API_KEY` |

---

## 2. Autentikasi

Endpoint **GET** bersifat publik (data terbuka, tanpa key).

Endpoint **POST / PUT / DELETE** wajib mengirim header:

```
X-API-KEY: isi_key_dari_env
```

Bisa juga memakai bentuk Bearer:

```
Authorization: Bearer isi_key_dari_env
```

Nilai key diambil dari `INFLASI_API_KEY` di file `.env`.

---

## 3. Daftar Endpoint

### 3.1 Cek Status

```
GET /api/v1/ping
```

### 3.2 Metadata (isi dropdown filter)

```
GET /api/v1/meta
```

Mengembalikan daftar wilayah, bulan, tahun, kelompok, dan komoditas yang ada di database.

### 3.3 Ringkasan Dashboard

```
GET /api/v1/ringkasan?tahun=2026&bulan=6
```

Satu panggilan berisi: angka IHK/MTM/YTD/YoY tiga wilayah, deret YoY untuk grafik garis, lima kelompok penyumbang andil tertinggi, dan hitungan komoditas stok waspada.

### 3.4 Indikator Inflasi

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/inflasi` | Daftar, mendukung paginasi |
| GET | `/inflasi/terbaru?tahun=2026` | Bulan terakhir tiap wilayah |
| GET | `/inflasi/{id}` | Detail |
| POST | `/inflasi` | Tambah (butuh key) |
| PUT | `/inflasi/{id}` | Ubah (butuh key) |
| DELETE | `/inflasi/{id}` | Hapus (butuh key) |

Parameter filter `GET /inflasi`:

| Parameter | Contoh | Arti |
|---|---|---|
| `wilayah` | `padang` | nasional / sumbar / padang |
| `tahun` | `2026` | Tahun data |
| `bulan` | `6` atau `Juni` | Angka atau nama bulan |
| `dari_bulan` / `sampai_bulan` | `1` / `6` | Rentang bulan |
| `sort` | `yoy` | Kolom pengurutan |
| `order` | `desc` | asc / desc |
| `per_page` | `50` | Maksimal 200 |

Contoh:

```
GET /api/v1/inflasi?wilayah=padang&tahun=2026&dari_bulan=1&sampai_bulan=6&sort=urutan_bulan
```

Body untuk POST/PUT:

```json
{
  "wilayah": "padang",
  "tahun": 2026,
  "urutan_bulan": 7,
  "bulan": "Juli",
  "ihk": 108.42,
  "mtm": 0.21,
  "ytd": 1.35,
  "yoy": 2.48
}
```

Field `bulan` boleh dikosongkan — akan diisi otomatis dari `urutan_bulan`.

### 3.5 Andil Inflasi

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/andil` | Daftar; filter `tahun`, `bulan`, `kelompok`, `q`, `tanpa_umum=1` |
| GET | `/andil/peringkat?tahun=2026&bulan=6&basis=andil_yoy&limit=5` | Kelompok tertinggi & terendah |
| GET | `/andil/{id}` | Detail |
| POST/PUT/DELETE | `/andil`, `/andil/{id}` | Butuh key |

### 3.6 Harga Pangan

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/harga-pangan` | Filter `tahun`, `bulan`, `minggu`, `komoditas`, `q` |
| GET | `/harga-pangan/terkini?tahun=2026` | Harga minggu terakhir semua komoditas |
| GET | `/harga-pangan/riwayat?komoditas=Beras Medium&tahun=2026` | Deret mingguan siap grafik |
| GET | `/harga-pangan/{id}` | Detail |
| POST/PUT/DELETE | — | Butuh key |

Saat POST/PUT, kolom `perubahan` dan `persen` dihitung otomatis bila `harga_ini` dan `harga_lalu` dikirim dan kedua kolom itu dibiarkan kosong.

### 3.7 Stok Pangan

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/stok-pangan` | Filter `tahun`, `bulan`, `komoditas`, `q` |
| GET | `/stok-pangan/ringkasan?tahun=2026&bulan=6` | Hitungan melimpah / aman / waspada |
| GET | `/stok-pangan/{id}` | Detail |
| POST | `/stok-pangan` | Memakai *updateOrCreate* karena ada unique (tahun, bulan, komoditas) |
| PUT/DELETE | `/stok-pangan/{id}` | Butuh key |

Setiap baris stok otomatis memuat `rasio` (stok ÷ kebutuhan × 100) dan `status`.

---

## 4. Contoh Pemakaian

### cURL — baca

```bash
curl "http://127.0.0.1:8000/api/v1/ringkasan?tahun=2026"
```

### cURL — tulis

```bash
curl -X POST "http://127.0.0.1:8000/api/v1/inflasi" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-KEY: isi_key_anda" \
  -d '{"wilayah":"padang","tahun":2026,"urutan_bulan":7,"ihk":108.42,"yoy":2.48}'
```

### JavaScript (fetch)

```js
const res = await fetch('http://127.0.0.1:8000/api/v1/inflasi?wilayah=padang&tahun=2026');
const json = await res.json();
console.log(json.data);
```

### PHP (Laravel HTTP client, dari aplikasi lain)

```php
$data = Http::withHeaders(['X-API-KEY' => env('INFLASI_API_KEY')])
    ->acceptJson()
    ->get('http://172.16.3.126:8222/api/v1/harga-pangan/terkini', ['tahun' => 2026])
    ->json();
```

---

## 5. Kode Status HTTP

| Kode | Arti |
|---|---|
| 200 | Berhasil |
| 201 | Data baru tersimpan |
| 401 | API key salah atau tidak dikirim |
| 404 | Data atau endpoint tidak ditemukan |
| 422 | Validasi gagal (lihat `errors`) |
| 500 | Kesalahan server |

---

## 6. Catatan Keamanan

- Jangan pernah commit `.env` ke repositori.
- Untuk produksi, ganti `allowed_origins` di `config/cors.php` dari `*` menjadi daftar domain yang benar-benar dipakai.
- Ganti API key secara berkala. Cukup ubah nilai `INFLASI_API_KEY` lalu jalankan `php artisan config:clear`.
- Bila nanti butuh banyak pengguna dengan token masing-masing, naikkan ke Laravel Sanctum (`composer require laravel/sanctum`) dan tukar middleware `api.key` dengan `auth:sanctum`.
