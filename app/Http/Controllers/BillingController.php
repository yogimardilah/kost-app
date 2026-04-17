<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Services\InvoiceService;
use Illuminate\Support\Str;
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

    public function edit(Billing $billing)
    {
        // Only owner can edit
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized action.');
        }

        return view('billings.edit', compact('billing'));
    }

    public function update(\Illuminate\Http\Request $request, Billing $billing)
    {
        // Only owner can update
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'periode_awal' => 'required|date',
            'periode_akhir' => 'required|date|after_or_equal:periode_awal',
            'total_tagihan' => 'required|numeric|min:0',
            'details' => 'nullable|array',
            'details.*.keterangan' => 'required|string',
            'details.*.qty' => 'required|numeric|min:0.01',
            'details.*.harga' => 'required|numeric|min:0',
            'details.*.subtotal' => 'required|numeric|min:0',
            'payments.*.tanggal_bayar' => 'required|date',
            'payments.*.jumlah' => 'required|numeric|min:0',
            'payments.*.metode' => 'required|in:tunai,transfer,qris',
            'payments.*.bukti_bayar_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Update billing main data
        $billing->update([
            'periode_awal' => $request->periode_awal,
            'periode_akhir' => $request->periode_akhir,
            'total_tagihan' => $request->total_tagihan,
        ]);

        // Update or create details
        if ($request->has('details')) {
            $existingDetailIds = [];
            
            foreach ($request->details as $detailData) {
                if (isset($detailData['id'])) {
                    // Update existing detail
                    $detail = \App\Models\BillingDetail::find($detailData['id']);
                    if ($detail && $detail->billing_id == $billing->id) {
                        $detail->update([
                            'keterangan' => $detailData['keterangan'],
                            'qty' => $detailData['qty'],
                            'harga' => $detailData['harga'],
                            'subtotal' => $detailData['subtotal'],
                        ]);
                        $existingDetailIds[] = $detail->id;
                    }
                } else {
                    // Create new detail
                    $newDetail = $billing->details()->create([
                        'keterangan' => $detailData['keterangan'],
                        'qty' => $detailData['qty'],
                        'harga' => $detailData['harga'],
                        'subtotal' => $detailData['subtotal'],
                    ]);
                    $existingDetailIds[] = $newDetail->id;
                }
            }
            
            // Delete details that are not in the submitted list
            $billing->details()->whereNotIn('id', $existingDetailIds)->delete();
        } else {
            // If no details submitted, delete all details
            $billing->details()->delete();
        }

        // Update or create payments
        if ($request->has('payments')) {
            $existingPaymentIds = [];
            
            foreach ($request->payments as $index => $paymentData) {
                $uploadedFile = $request->file("payments.$index.bukti_bayar_file");

                if (isset($paymentData['id'])) {
                    // Update existing payment
                    $payment = \App\Models\Payment::find($paymentData['id']);
                    if ($payment && $payment->billing_id == $billing->id) {
                        $payload = [
                            'tanggal_bayar' => $paymentData['tanggal_bayar'],
                            'jumlah' => $paymentData['jumlah'],
                            'metode' => $paymentData['metode'],
                        ];

                        if ($uploadedFile) {
                            $extension = $uploadedFile->getClientOriginalExtension();
                            $uniqueName = Str::uuid() . '.' . $extension;
                            $path = $uploadedFile->storeAs('payments', $uniqueName, 'public');
                            $payload['bukti_bayar'] = $path;
                        }

                        $payment->update($payload);

                        $existingPaymentIds[] = $payment->id;
                    }
                } else {
                    if (!$uploadedFile) {
                        return back()
                            ->withErrors(["payments.$index.bukti_bayar_file" => 'Upload bukti pembayaran wajib diisi'])
                            ->withInput();
                    }

                    $extension = $uploadedFile->getClientOriginalExtension();
                    $uniqueName = Str::uuid() . '.' . $extension;
                    $path = $uploadedFile->storeAs('payments', $uniqueName, 'public');

                    // Create new payment
                    $newPayment = $billing->payments()->create([
                        'tanggal_bayar' => $paymentData['tanggal_bayar'],
                        'jumlah' => $paymentData['jumlah'],
                        'metode' => $paymentData['metode'],
                        'bukti_bayar' => $path,
                    ]);
                    $existingPaymentIds[] = $newPayment->id;
                }
            }
            
            // Delete payments that are not in the submitted list
            $billing->payments()->whereNotIn('id', $existingPaymentIds)->delete();
        }

        // Auto-update status based on payments
        $billing->updateStatus();

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Billing dan detail berhasil diupdate!');
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
