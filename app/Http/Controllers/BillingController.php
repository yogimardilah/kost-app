<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Services\InvoiceService;
use PDF;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with('room','consumer')->orderBy('id','desc')->get();
        return view('billings.index', compact('billings'));
    }

    public function show(Billing $billing)
    {
        return view('billings.show', compact('billing'));
    }

    /**
     * Download invoice as PDF.
     */
    public function downloadInvoice(Billing $billing)
    {
        try {
            $html = InvoiceService::generateInvoiceHtml($billing);
            $pdf = PDF::loadHTML($html)
                ->setPaper('a4')
                ->setOption('margin-top', 0)
                ->setOption('margin-right', 0)
                ->setOption('margin-bottom', 0)
                ->setOption('margin-left', 0);

            return $pdf->download('Invoice-' . $billing->invoice_number . '.pdf');
        } catch (\Exception $e) {
            return redirect()->route('billings.show', $billing)
                ->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Show all billing reminders.
     */
    public function reminders()
    {
        $summary = \App\Services\ReminderService::getReminderSummary();
        return view('billings.reminders', compact('summary'));
    }
}
