<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billings';

    protected $fillable = [
        'invoice_number',
        'consumer_id',
        'room_id',
        'periode_awal',
        'periode_akhir',
        'total_tagihan',
        'status',
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'periode_awal' => 'date',
        'periode_akhir' => 'date',
    ];

    /**
     * Billing belongs to a consumer.
     */
    public function consumer()
    {
        return $this->belongsTo(Consumer::class);
    }

    /**
     * Billing belongs to a room.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Billing has many details.
     */
    public function details()
    {
        return $this->hasMany(BillingDetail::class);
    }

    /**
     * Billing has many payments.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
