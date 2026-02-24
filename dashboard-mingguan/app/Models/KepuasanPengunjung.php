<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KepuasanPengunjung extends Model
{
    use HasFactory;

    protected $table = 'kepuasan_pengunjung';

    protected $fillable = [
        'tanggal_target',
        'jenis_kelamin',
        'sangat_puas',
        'puas',
        'tidak_puas',
        'sangat_tidak_puas',
    ];
}
