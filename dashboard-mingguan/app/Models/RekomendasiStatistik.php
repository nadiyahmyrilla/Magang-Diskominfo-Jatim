<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekomendasiStatistik extends Model
{
    protected $table = 'rekomendasi_statistik';

    protected $fillable = [
        'tanggal_target',
        'instansi_tujuan',
        'Batal',
        'Layak',
        'Pemeriksaan',
        'Pengajuan',
        'Pengesahan',
        'Perbaikan',
        'Total',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
    ];
}
