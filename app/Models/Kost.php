<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kost extends Model
{
    protected $table = 'kosts'; // 🔥 WAJIB

    protected $fillable = [
        'nama_kost',
        'alamat',
        'kota',
        'provinsi',
        'telepon',
        'email',
        'deskripsi',
    ];

    /**
     * Rooms that belong to this kost.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
