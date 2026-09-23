<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaPangan extends Model
{
    protected $fillable = [
        'bulan', 'urutan_bulan', 'tahun', 'komoditas', 'minggu', 'satuan',
        'het', 'harga_ini', 'harga_lalu', 'perubahan', 'persen',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'het' => 'float',
        'harga_ini' => 'float',
        'harga_lalu' => 'float',
        'perubahan' => 'float',
        'persen' => 'float',
    ];
}
