<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InflasiIndikator extends Model
{
    protected $fillable = [
        'wilayah', 'bulan', 'urutan_bulan', 'tahun', 'ihk', 'mtm', 'ytd', 'yoy',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'ihk' => 'float',
        'mtm' => 'float',
        'ytd' => 'float',
        'yoy' => 'float',
    ];
}
