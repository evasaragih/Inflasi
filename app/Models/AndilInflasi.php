<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AndilInflasi extends Model
{
    protected $fillable = [
        'bulan', 'urutan_bulan', 'tahun', 'kelompok', 'mtm', 'ytd', 'yoy', 'andil_mtm', 'andil_yoy',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'mtm' => 'float',
        'ytd' => 'float',
        'yoy' => 'float',
        'andil_mtm' => 'float',
        'andil_yoy' => 'float',
    ];
}
