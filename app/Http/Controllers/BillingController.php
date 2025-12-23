<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Services\InvoiceService;
use PDF;

class BillingController extends Controller
{
    public function index()
    {
        $query = Billing::with(['room','consumer'])->orderByDesc('created_at');

        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhereHas('consumer', function ($c) use ($s) {
                        $c->where('nama', 'like', "%{$s}%");
                    });
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('start_date')) {
            $query->whereDate('created_at', '>=', request('start_date'));
        }
        if (request()->filled('end_date')) {
            $query->whereDate('created_at', '<=', request('end_date'));
        }

        $billings = $query->paginate(15)->withQueryString();
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
                ->setOption('margin-top', 10)
                ->setOption('margin-right', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10)
                ->setOption('enable-local-file-access', true);

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
