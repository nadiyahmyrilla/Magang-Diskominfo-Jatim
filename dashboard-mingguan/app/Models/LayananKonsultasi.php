<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'layanan_konsultasi';

    protected $fillable = [
        'tanggal_target',
        'perangkat_daerah',
        'laki_laki',
        'perempuan',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
    ];
}
