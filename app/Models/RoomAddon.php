<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAddon extends Model
{
    use HasFactory;

    protected $table = 'room_addons';

    protected $fillable = [
        'nama_addon',
        'harga',
        'satuan',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    /**
     * Rooms that use this addon.
     */
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_addon_maps', 'addon_id', 'room_id');
    }
}
