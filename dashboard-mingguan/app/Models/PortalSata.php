<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalSata extends Model
{
    protected $table = 'portal_sata';

    protected $fillable = [
        'tanggal_target',
        'nama_dataset',
        'dataset',
        'target_total',
        'capaian',
        'capaian(%)',
    ];

    protected $casts = [
        'tanggal_target' => 'date',
        'capaian(%)' => 'decimal:2',
    ];
}
