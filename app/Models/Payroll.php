<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'slip_number',
        'employee_id',
        'bulan',
        'tahun',
        'gaji_pokok',
        'bonus',
        'potongan',
        'total_gaji',
        'tanggal_bayar',
        'status',
        'keterangan',
        'file_path',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'gaji_pokok' => 'decimal:2',
        'bonus' => 'decimal:2',
        'potongan' => 'decimal:2',
        'total_gaji' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getBulanNamaAttribute()
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanNames[$this->bulan] ?? '';
    }

    public function getPeriodeAttribute()
    {
        return $this->bulan_nama . ' ' . $this->tahun;
    }
}
