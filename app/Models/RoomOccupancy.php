<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomOccupancy extends Model
{
    use HasFactory;

    protected $fillable = ['room_id','consumer_id','tanggal_masuk','tanggal_keluar','status'];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }
}
