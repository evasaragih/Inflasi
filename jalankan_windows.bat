@echo off
title Menjalankan Dashboard Inflasi Kota Padang
color 0B
echo ====================================================================
echo           MENJALANKAN DASHBOARD INFLASI KOTA PADANG
echo ====================================================================
echo.
cd /d "%~dp0"

echo Membuka browser ke http://localhost:8000 ...
start http://localhost:8000

echo Menjalankan server Laravel di port 8000...
echo (Tekan Ctrl+C untuk menghentikan server)
echo.

php artisan serve --port=8000

pause
