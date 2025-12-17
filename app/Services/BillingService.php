<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingDetail;
use App\Models\RoomOccupancy;
use Carbon\Carbon;

class BillingService
{
    /**
     * Generate a billing record for a newly checked-in occupancy.
     * Billing includes room rent + any addons.
     *
     * @param RoomOccupancy $occupancy
     * @return Billing
     */
    public static function generateBillingForOccupancy(RoomOccupancy $occupancy): Billing
    {
        $invoiceNumber = static::generateInvoiceNumber();
        $totalTagihan = 0;

        // Create billing record
        $billing = Billing::create([
            'invoice_number'  => $invoiceNumber,
            'consumer_id'     => $occupancy->consumer_id,
            'room_id'         => $occupancy->room_id,
            'periode_awal'    => now()->startOfMonth(),
            'periode_akhir'   => now()->endOfMonth(),
            'total_tagihan'   => 0, // Will update after details
            'status'          => 'pending',
        ]);

        // Add room rent as first billing detail
        $roomHarga = $occupancy->room->harga ?? 0;
        if ($roomHarga > 0) {
            BillingDetail::create([
                'billing_id'  => $billing->id,
                'keterangan'  => 'Sewa Kamar ' . $occupancy->room->nomor_kamar,
                'qty'         => 1,
                'harga'       => $roomHarga,
                'subtotal'    => $roomHarga,
            ]);
            $totalTagihan += $roomHarga;
        }

        // Add addons as billing details
        $addons = $occupancy->room->addons()->get();
        foreach ($addons as $addon) {
            $addonHarga = $addon->harga ?? 0;
            if ($addonHarga > 0) {
                BillingDetail::create([
                    'billing_id'  => $billing->id,
                    'keterangan'  => $addon->nama_addon,
                    'qty'         => 1,
                    'harga'       => $addonHarga,
                    'subtotal'    => $addonHarga,
                ]);
                $totalTagihan += $addonHarga;
            }
        }

        // Update billing total
        $billing->update(['total_tagihan' => $totalTagihan]);

        return $billing;
    }

    /**
     * Generate a unique invoice number.
     * Format: INV-YYYYMMDD-XXXXX
     *
     * @return string
     */
    protected static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Billing::whereDate('created_at', today())->count() + 1;
        return sprintf('INV-%s-%05d', $date, $count);
    }
}
