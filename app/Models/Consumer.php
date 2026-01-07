<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;

    protected $fillable = ['nik','nama','no_hp','kendaraan','tanda_pengenal','kontak_darurat_nama','kontak_darurat_hubungan','kontak_darurat_no_hp'];

    public function occupancies()
    {
        return $this->hasMany(RoomOccupancy::class);
    }
}
