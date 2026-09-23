<?php

if (! function_exists('setting')) {
    /**
     * Ambil nilai pengaturan situs (CMS) berdasarkan key, dengan nilai default.
     */
    function setting(string $key, ?string $default = null): ?string
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (! function_exists('fnum')) {
    /**
     * Format angka gaya Indonesia (koma sebagai desimal, titik sebagai ribuan).
     */
    function fnum($value, int $decimals = 2): string
    {
        if ($value === null) {
            return '-';
        }

        return number_format((float) $value, $decimals, ',', '.');
    }
}

if (! function_exists('fsign')) {
    /**
     * Format angka dengan tanda + di depan nilai positif. Cocok untuk MTM/YTD/YoY.
     */
    function fsign($value, int $decimals = 2): string
    {
        if ($value === null) {
            return '-';
        }

        $sign = $value > 0 ? '+' : '';

        return $sign . number_format((float) $value, $decimals, ',', '.');
    }
}
