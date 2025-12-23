<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonTransaction extends Model
{
    use HasFactory;

    protected $table = 'addon_transactions';

    protected $fillable = [
        'consumer_id',
        'room_id',
        'invoice_number',
        'tanggal',
        'status',
        'total',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function details()
    {
        return $this->hasMany(AddonTransactionDetail::class);
    }
}
