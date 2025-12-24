<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kamar',
        'jenis_kamar',
        'harga',
        'harga_harian',
        'fasilitas',
        'status',
        'kost_id',
    ];

    protected $casts = [
        'harga' => 'decimal:0',
        'harga_harian' => 'decimal:0',
    ];

    // Relations
    public function kost()
    {
        return $this->belongsTo(Kost::class);
    }

    public function occupancies()
    {
        return $this->hasMany(RoomOccupancy::class);
    }

    public function addons()
    {
        return $this->belongsToMany(RoomAddon::class, 'room_addon_maps', 'room_id', 'addon_id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }

    // Helper
    public function isAvailable(): bool
    {
        return $this->status === 'tersedia';
    }
}
