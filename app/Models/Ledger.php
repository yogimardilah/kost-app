<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $table = 'ledgers';

    protected $fillable = [
        'consumer_id',
        'billing_id',
        'billing_detail_id',
        'payment_id',
        'room_id',
        'occupancy_id',
        'tanggal',
        'tipe',
        'nominal',
        'keterangan',
        'meta',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'nominal' => 'decimal:2',
        'meta' => 'array',
    ];

    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function billingDetail()
    {
        return $this->belongsTo(BillingDetail::class, 'billing_detail_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function occupancy()
    {
        return $this->belongsTo(RoomOccupancy::class, 'occupancy_id');
    }
}
