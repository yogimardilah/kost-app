<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'tanggal_bergabung',
        'tanggal_berakhir',
        'gaji',
        'tanggal_gajian',
        'no_hp',
        'alamat',
        'foto',
        'status',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_berakhir' => 'date',
        'gaji' => 'decimal:2',
    ];
}
