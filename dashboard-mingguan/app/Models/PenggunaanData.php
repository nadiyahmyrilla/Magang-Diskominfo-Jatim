<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenggunaanData extends Model
{
    use HasFactory;

    protected $table = 'penggunaan_data';

    protected $fillable = [
        'periode_awal',
        'periode_akhir',
        'tanggal',
        'view',
        'download',
    ];

    protected $casts = [
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
        'tanggal' => 'date',
    ];
}
