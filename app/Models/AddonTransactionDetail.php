<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddonTransactionDetail extends Model
{
    use HasFactory;

    protected $table = 'addon_transaction_details';

    protected $fillable = [
        'addon_transaction_id',
        'addon_id',
        'nama_addon',
        'qty',
        'harga',
        'subtotal',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(AddonTransaction::class, 'addon_transaction_id');
    }

    public function addon()
    {
        return $this->belongsTo(RoomAddon::class, 'addon_id');
    }
}
