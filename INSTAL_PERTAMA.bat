@echo off
title Instalasi Pertama Dashboard Inflasi Kota Padang
color 0B
cls

echo ================================================================
echo   INSTALASI PERTAMA - DASHBOARD INFLASI KOTA PADANG
echo ================================================================
echo.
echo Mempersiapkan aplikasi untuk pertama kali dijalankan di PC ini...
echo.

if not exist ".env" (
    echo [1/5] Membuat file .env...
    copy .env.example .env
) else (
    echo [1/5] File .env sudah ada.
)

echo [2/5] Menghasilkan Kunci Aplikasi (APP_KEY)...
php artisan key:generate --force

echo [3/5] Mempersiapkan File Database SQLite...
if not exist "database" mkdir database
if not exist "database\database.sqlite" type NUL > database\database.sqlite

echo [4/5] Memasang Tabel & Data Seeder (Inflasi, Andil, Pangan 2024-2026)...
php artisan migrate:fresh --seed --force

echo [5/5] Membuat Link Storage & Membersihkan Cache...
php artisan storage:link >NUL 2>&1
php artisan config:clear >NUL 2>&1
php artisan cache:clear >NUL 2>&1

echo.
echo ================================================================
echo   INSTALASI SUKSES 100%!
echo ================================================================
echo.
echo Sekarang Anda dapat menjalankan aplikasi cukup dengan melakukan
echo DOBEL KLIK pada file: "JALANKAN_WEB.bat"
echo.
echo ----------------------------------------------------------------
echo   URL UTAMA WEB   : http://localhost:8000
echo   URL PANEL ADMIN : http://localhost:8000/admin
echo   EMAIL ADMIN     : admin@padang.go.id
echo   PASSWORD ADMIN  : admin123
echo ----------------------------------------------------------------
echo.
pause
