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

        // Determine period from occupancy dates
        $start = $occupancy->tanggal_masuk ? Carbon::parse($occupancy->tanggal_masuk) : now();
        $end = $occupancy->tanggal_keluar ? Carbon::parse($occupancy->tanggal_keluar) : $start->copy()->addDays(30);
        $days = max(1, $start->diffInDays($end));

        // Create billing record with occupancy period
        $billing = Billing::create([
            'invoice_number'  => $invoiceNumber,
            'consumer_id'     => $occupancy->consumer_id,
            'room_id'         => $occupancy->room_id,
            'periode_awal'    => $start->toDateString(),
            'periode_akhir'   => $end->toDateString(),
            'total_tagihan'   => 0, // Will update after details
            'status'          => 'pending',
        ]);

        // Add room rent as first billing detail (daily vs monthly with proration)
        $roomMonthly = $occupancy->room->harga ?? 0;
        $roomDaily = $occupancy->room->harga_harian ?? 0;
        $tipeHarga = $occupancy->room->tipe_harga ?? 'bulanan'; // Get room pricing type
        
        // Use room's tipe_harga to determine pricing, not duration
        if ($tipeHarga === 'harian' && $roomDaily > 0) {
            // Daily pricing
            $unit = (int) $roomDaily;
            $qty = $days;
            $subtotal = $unit * $qty;
            BillingDetail::create([
                'billing_id'  => $billing->id,
                'keterangan'  => 'Sewa Kamar ' . ($occupancy->room->nomor_kamar ?? '-') . ' - Harian (' . $qty . ' hari)',
                'qty'         => $qty,
                'harga'       => $unit,
                'subtotal'    => $subtotal,
            ]);
            $totalTagihan += $subtotal;
        } elseif ($roomMonthly > 0) {
            // Monthly pricing with proration based on actual days
            $daysInMonth = $start->daysInMonth;
            $hargaPerHari = $roomMonthly / $daysInMonth;
            $subtotalRaw = $hargaPerHari * $days;
            $subtotal = (int) (round($subtotalRaw / 100) * 100);
            
            BillingDetail::create([
                'billing_id'  => $billing->id,
                'keterangan'  => 'Sewa Kamar ' . ($occupancy->room->nomor_kamar ?? '-') . ' - Bulanan (Prorate: ' . $days . ' hari dari ' . $daysInMonth . ' hari)',
                'qty'         => $days,
                'harga'       => (int) round($hargaPerHari),
                'subtotal'    => $subtotal,
            ]);
            $totalTagihan += $subtotal;
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
