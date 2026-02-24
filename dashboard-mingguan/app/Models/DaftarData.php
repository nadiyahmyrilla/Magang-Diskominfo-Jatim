<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarData extends Model
{
    use HasFactory;

    protected $table = 'daftar_data';

    protected $fillable = [
        'perangkat_daerah',
        'jumlah',
        'tanggal_target',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
    ];
}
