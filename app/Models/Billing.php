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

    /**
     * Get total amount paid for this billing.
     */
    public function getTotalDibayarAttribute()
    {
        $sum = (float) $this->payments()->sum('jumlah');
        return round($sum, 0);
    }

    /**
     * Get remaining amount to be paid.
     */
    public function getSisaTagihanAttribute()
    {
        $totalTagihan = round((float) ($this->total_tagihan ?? 0), 0);
        $totalDibayar = round((float) ($this->total_dibayar ?? 0), 0);
        return max(0, $totalTagihan - $totalDibayar);
    }

    /**
     * Get overpaid amount (credit balance) for this billing.
     */
    public function getKelebihanBayarAttribute()
    {
        $totalTagihan = round((float) ($this->total_tagihan ?? 0), 0);
        $totalDibayar = round((float) ($this->total_dibayar ?? 0), 0);
        return max(0, $totalDibayar - $totalTagihan);
    }

    /**
     * Update billing status based on payments.
     */
    public function updateStatus()
    {
        $totalDibayar = round((float) $this->total_dibayar, 0);
        $totalTagihan = round((float) $this->total_tagihan, 0);

        if ($totalDibayar == 0) {
            $this->status = 'pending';
        } elseif ($totalDibayar >= $totalTagihan) {
            $this->status = 'lunas';
        } else {
            $this->status = 'sebagian';
        }

        $this->save();
    }
}
