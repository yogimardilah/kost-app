<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $table = 'kosts'; // 🔥 WAJIB

    protected $fillable = [
        'nama_kost',
        'alamat',
        'pemilik_id',
        'harga',
        'jumlah_kamar',
        'status',
    ];
}
