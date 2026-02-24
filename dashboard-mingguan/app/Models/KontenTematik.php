<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenTematik extends Model
{
    use HasFactory;

    protected $table = 'konten_tematik';

    protected $fillable = [
        'tanggal_target',
        'agenda',
        'progress',
        'data_dukung',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
        'progress' => 'decimal:2',
    ];
}
