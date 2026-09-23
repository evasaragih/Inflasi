@echo off
title Setup Dashboard Inflasi Kota Padang
color 0A
echo ====================================================================
echo        SKRIP SETUP OTOMATIS - DASHBOARD INFLASI KOTA PADANG
echo ====================================================================
echo.

cd /d "%~dp0"

echo [1/5] Memeriksa file .env...
if not exist .env (
    echo       Membuat file .env dari .env.example...
    copy .env.example .env
) else (
    echo       File .env sudah ada.
)
echo.

echo [2/5] Menginstal dependensi Composer...
call composer config audit.block-insecure false
call composer install
if %ERRORLEVEL% NEQ 0 (
    echo.
    echo [ERROR] Gagal menginstal dependensi composer. Pastikan Composer dan PHP >= 8.2 sudah terinstall!
    pause
    exit /b %ERRORLEVEL%
)
echo.

echo [3/5] Membuat Application Key...
call php artisan key:generate
echo.

echo [4/5] Membuat link penyimpanan (storage:link)...
if exist public\storage (
    rmdir /s /q public\storage
)
call php artisan storage:link
echo.

echo [5/5] Memeriksa Database SQLite...
if not exist database\database.sqlite (
    echo       Membuat file database.sqlite kosong...
    type nul > database\database.sqlite
    echo       Mengisi data awal database...
    call php artisan migrate:fresh --seed
) else (
    echo       Database database.sqlite sudah tersedia.
)
echo.

echo ====================================================================
echo  [SELESAI] Setup berhasil diselesaikan!
echo.
echo  Untuk menjalankan aplikasi:
echo  1. Klik 2x pada file "jalankan_windows.bat"
echo  2. Atau ketik di CMD: php artisan serve
echo  3. Buka browser: http://localhost:8000
echo ====================================================================
echo.
pause
