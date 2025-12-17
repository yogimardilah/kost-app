<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;

    protected $fillable = ['nik','nama','no_hp','kendaraan'];

    public function occupancies()
    {
        return $this->hasMany(RoomOccupancy::class);
    }
}
