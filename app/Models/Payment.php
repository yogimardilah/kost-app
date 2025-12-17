<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'billing_id',
        'tanggal_bayar',
        'jumlah',
        'metode',
        'bukti_bayar',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Payment belongs to a Billing.
     */
    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    /**
     * Payment has a consumer through billing.
     */
    public function consumer()
    {
        return $this->hasOneThrough(Consumer::class, Billing::class, 'id', 'id', 'billing_id', 'consumer_id');
    }
}
