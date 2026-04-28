<?php

namespace App\Console\Commands;

use App\Models\Billing;
use App\Models\BillingDetail;
use App\Models\Ledger;
use App\Models\Payment;
use Illuminate\Console\Command;

class LedgerBackfillCommand extends Command
{
    protected $signature = 'ledger:backfill {--dry-run : Only show counts, do not write}';

    protected $description = 'Backfill ledgers from existing billing_details and payments.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $missingDetailCount = BillingDetail::leftJoin('ledgers', 'ledgers.billing_detail_id', '=', 'billing_details.id')
            ->whereNull('ledgers.id')
            ->count();

        $missingPaymentCount = Payment::leftJoin('ledgers', 'ledgers.payment_id', '=', 'payments.id')
            ->whereNull('ledgers.id')
            ->count();

        $this->info("Missing ledger rows: billing_details={$missingDetailCount}, payments={$missingPaymentCount}");

        if ($dryRun) {
            $this->info('Dry run enabled, nothing written.');
            return self::SUCCESS;
        }

        $this->info('Backfilling billing_details...');
        BillingDetail::with('billing')->orderBy('id')->chunk(500, function ($details) {
            foreach ($details as $detail) {
                if (!$detail->billing) {
                    continue;
                }

                // Skip if already exists
                if (Ledger::where('billing_detail_id', $detail->id)->exists()) {
                    continue;
                }

                $subtotal = (float) ($detail->subtotal ?? 0);
                $tipe = $subtotal >= 0 ? 'debit' : 'kredit';
                $nominal = abs($subtotal);

                Ledger::create([
                    'consumer_id' => $detail->billing->consumer_id,
                    'billing_id' => $detail->billing_id,
                    'billing_detail_id' => $detail->id,
                    'payment_id' => null,
                    'room_id' => $detail->billing->room_id,
                    'occupancy_id' => null,
                    'tanggal' => $detail->created_at ?? $detail->billing->created_at,
                    'tipe' => $tipe,
                    'nominal' => $nominal,
                    'keterangan' => $detail->keterangan,
                    'meta' => [
                        'source' => 'backfill',
                    ],
                ]);
            }
        });

        $this->info('Backfilling payments...');
        Payment::with('billing')->orderBy('id')->chunk(500, function ($payments) {
            foreach ($payments as $payment) {
                if (!$payment->billing) {
                    continue;
                }

                if (Ledger::where('payment_id', $payment->id)->exists()) {
                    continue;
                }

                Ledger::create([
                    'consumer_id' => $payment->billing->consumer_id,
                    'billing_id' => $payment->billing_id,
                    'billing_detail_id' => null,
                    'payment_id' => $payment->id,
                    'room_id' => $payment->billing->room_id,
                    'occupancy_id' => null,
                    'tanggal' => $payment->tanggal_bayar ?? $payment->created_at,
                    'tipe' => 'kredit',
                    'nominal' => (float) ($payment->jumlah ?? 0),
                    'keterangan' => 'Pembayaran ' . ($payment->metode ?? '-'),
                    'meta' => [
                        'source' => 'backfill',
                    ],
                ]);
            }
        });

        $this->info('Backfill done.');

        return self::SUCCESS;
    }
}
