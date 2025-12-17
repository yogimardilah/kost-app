<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingReminder extends Model
{
    use HasFactory;

    protected $table = 'billing_reminders';

    protected $fillable = [
        'billing_id',
        'days_overdue',
        'note',
        'is_sent',
        'sent_at',
    ];

    protected $casts = [
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * BillingReminder belongs to a Billing.
     */
    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}
