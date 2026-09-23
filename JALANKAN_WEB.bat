@echo off
title Dashboard Inflasi Kota Padang - Server Web
color 0A
cls

echo ================================================================
echo        DASHBOARD INFLASI & HARGA PANGAN KOTA PADANG
echo ================================================================
echo.
echo [1/4] Memeriksa Konfigurasi Lingkungan (.env)...
if not exist ".env" (
    echo [.env tidak ditemukan, membuat dari .env.example...]
    copy .env.example .env
)

echo.
echo [2/4] Memeriksa Database SQLite...
if not exist "database\database.sqlite" (
    echo [Membuat file database\database.sqlite...]
    type NUL > database\database.sqlite
    echo [Menjalankan migrasi & data seeder awal...]
    php artisan migrate:fresh --seed --force
)

echo.
echo [3/4] Menghubungkan Storage & Kunci Aplikasi...
php artisan key:generate --force >NUL 2>&1
php artisan storage:link >NUL 2>&1

echo.
echo [4/4] Membuka Browser & Menjalankan Server Web...
echo.
echo ----------------------------------------------------------------
echo   ALAMAT WEB     : http://localhost:8000
echo   PANEL ADMIN    : http://localhost:8000/admin
echo   LOGIN ADMIN    : admin@padang.go.id / admin123
echo ----------------------------------------------------------------
echo.
echo Tekan CTRL + C di jendela ini untuk menghentikan server web.
echo.

start http://localhost:8000
php artisan serve --port=8000

pause
