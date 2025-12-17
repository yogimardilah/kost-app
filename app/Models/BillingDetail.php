<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingDetail extends Model
{
    use HasFactory;

    protected $table = 'billing_details';

    protected $fillable = [
        'billing_id',
        'keterangan',
        'qty',
        'harga',
        'subtotal',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * BillingDetail belongs to a Billing.
     */
    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}
