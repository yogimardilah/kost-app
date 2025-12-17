<?php

namespace App\Services;

use App\Models\Billing;
use App\Models\BillingReminder;
use Carbon\Carbon;

class ReminderService
{
    /**
     * Generate reminders for unpaid/partially paid billings.
     * Run this daily to check for overdue invoices.
     */
    public static function generateReminders(): int
    {
        $count = 0;

        // Get all unpaid/partially paid billings
        $unpaidBillings = Billing::whereIn('status', ['pending', 'sebagian'])
            ->where('periode_akhir', '<', now())
            ->get();

        foreach ($unpaidBillings as $billing) {
            $daysOverdue = now()->diffInDays($billing->periode_akhir);

            // Check if reminder already exists for today
            $existingReminder = BillingReminder::where('billing_id', $billing->id)
                ->whereDate('created_at', today())
                ->first();

            if (!$existingReminder) {
                BillingReminder::create([
                    'billing_id' => $billing->id,
                    'days_overdue' => $daysOverdue,
                    'note' => "Tagihan jatuh tempo telah berlalu {$daysOverdue} hari",
                    'is_sent' => false,
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get active reminders for unpaid billings.
     */
    public static function getActiveReminders()
    {
        return BillingReminder::where('is_sent', false)
            ->with('billing.consumer', 'billing.room')
            ->orderBy('days_overdue', 'desc')
            ->get();
    }

    /**
     * Mark reminder as sent.
     */
    public static function markAsSent(BillingReminder $reminder): void
    {
        $reminder->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Get reminder summary for dashboard.
     */
    public static function getReminderSummary(): array
    {
        $reminders = BillingReminder::where('is_sent', false)->get();

        return [
            'total_reminders' => $reminders->count(),
            'critical' => $reminders->filter(fn($r) => $r->days_overdue > 7)->count(), // >7 hari
            'warning' => $reminders->filter(fn($r) => $r->days_overdue <= 7 && $r->days_overdue > 0)->count(), // 0-7 hari
            'reminders' => $reminders,
        ];
    }
}
