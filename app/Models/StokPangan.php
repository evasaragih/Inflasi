<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokPangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'urutan_bulan',
        'bulan',
        'komoditas',
        'stok',
        'kebutuhan',
        'satuan',
        'harga_pasar',
        'het',
        'keterangan',
    ];

    public function getRasioAttribute(): float
    {
        $kebutuhan = $this->kebutuhan > 0 ? $this->kebutuhan : 1;
        return round(($this->stok / $kebutuhan) * 100, 1);
    }

    public function getStatusAttribute(): string
    {
        $rasio = $this->rasio;
        if ($rasio >= 115) {
            return 'Melimpah';
        } elseif ($rasio >= 98) {
            return 'Aman (Cukup)';
        } else {
            return 'Waspada / Perlu Pasokan';
        }
    }

    public function getBadgeClassAttribute(): string
    {
        $rasio = $this->rasio;
        if ($rasio >= 115) {
            return 'bg-hijau/10 text-hijau border-hijau/20';
        } elseif ($rasio >= 98) {
            return 'bg-blue-500/10 text-blue-700 border-blue-200';
        } else {
            return 'bg-marun/10 text-marun border-marun/20';
        }
    }
}
