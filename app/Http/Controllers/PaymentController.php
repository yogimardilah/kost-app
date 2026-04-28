<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Billing;
use App\Models\Ledger;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $query = Payment::with(['billing.consumer'])
            ->orderByDesc('tanggal_bayar')
            ->orderByDesc('id');

        if (request()->filled('search')) {
            $s = request('search');
            $query->where(function ($q) use ($s) {
                $q->whereHas('billing', function ($b) use ($s) {
                    $b->where('invoice_number', 'like', "%{$s}%")
                        ->orWhereHas('consumer', function ($c) use ($s) {
                            $c->where('nama', 'like', "%{$s}%");
                        });
                })
                ->orWhere('metode', 'like', "%{$s}%");
            });
        }

        if (request()->filled('start_date')) {
            $query->whereDate('tanggal_bayar', '>=', request('start_date'));
        }
        if (request()->filled('end_date')) {
            $query->whereDate('tanggal_bayar', '<=', request('end_date'));
        }

        $payments = $query->paginate(15)->withQueryString();
        return view('payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    /**
     * Show form to create a payment for a specific billing.
     */
    public function create(Request $request)
    {
        $billing = null;
        $billingDetails = [];
        $totalPaid = 0;
        $remaining = 0;
        $overpaid = 0;
        $computedStatus = null;
        $detailAllocations = [];
        
        if ($request->filled('billing')) {
            $billing = Billing::with(['consumer', 'room', 'details'])->find($request->get('billing'));
            if ($billing) {
                $billingDetails = $billing->details;
                $totalPaid = (float) Payment::where('billing_id', $billing->id)->sum('jumlah');
                $totalPaid = round($totalPaid, 0);
                $totalTagihan = round((float) $billing->total_tagihan, 0);
                $remaining = max(0, $totalTagihan - $totalPaid);
                $overpaid = max(0, $totalPaid - $totalTagihan);

                if ($totalTagihan <= 0) {
                    $computedStatus = 'lunas';
                } elseif ($totalPaid <= 0) {
                    $computedStatus = 'pending';
                } elseif ($totalPaid >= $totalTagihan) {
                    $computedStatus = 'lunas';
                } else {
                    $computedStatus = 'sebagian';
                }

                // Allocate payments across details top-to-bottom for clearer per-row status.
                $pool = $totalPaid;
                foreach ($billingDetails as $detail) {
                    $subtotal = round((float) ($detail->subtotal ?? 0), 0);
                    $paidForRow = min($pool, $subtotal);
                    $rowRemaining = max(0, $subtotal - $paidForRow);
                    $pool -= $paidForRow;

                    if ($subtotal <= 0) {
                        $rowStatus = 'n/a';
                    } elseif ($rowRemaining <= 0) {
                        $rowStatus = 'lunas';
                    } elseif ($paidForRow > 0) {
                        $rowStatus = 'sebagian';
                    } else {
                        $rowStatus = 'belum';
                    }

                    $detailAllocations[$detail->id] = [
                        'paid' => $paidForRow,
                        'remaining' => $rowRemaining,
                        'status' => $rowStatus,
                    ];
                }
            }
        }

        $billings = Billing::where('status', '!=', 'lunas')
            ->with('consumer')
            ->orderBy('id', 'desc')
            ->get();
        return view('payments.create', compact('billings', 'billing', 'billingDetails', 'totalPaid', 'remaining', 'overpaid', 'computedStatus', 'detailAllocations'));
    }

    /**
     * Store a newly recorded payment.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $billing = Billing::find($data['billing_id']);
        if (!$billing) {
            return back()->withErrors(['billing_id' => 'Billing tidak ditemukan'])->withInput();
        }

        $totalPaid = (float) Payment::where('billing_id', $billing->id)->sum('jumlah');
        $totalPaid = round($totalPaid, 0);
        $totalTagihan = round((float) $billing->total_tagihan, 0);
        $remaining = max(0, $totalTagihan - $totalPaid);

        // Normalize to whole Rupiah
        $data['jumlah'] = round((float) $data['jumlah'], 0);

        // handle file upload (optional)
        $note = $data['bukti_bayar'] ?? null;
        if ($request->hasFile('bukti_bayar_file')) {
            $file = $request->file('bukti_bayar_file');
            $extension = $file->getClientOriginalExtension();
            $uniqueName = Str::uuid() . '.' . $extension;
            $path = $file->storeAs('payments', $uniqueName, 'public');
            $data['bukti_bayar'] = $note ? ($note . ' | file:' . $path) : $path;
        }

        $payment = Payment::create($data);

        Ledger::create([
            'consumer_id' => $billing->consumer_id,
            'billing_id' => $billing->id,
            'billing_detail_id' => null,
            'payment_id' => $payment->id,
            'room_id' => $billing->room_id,
            'occupancy_id' => null,
            'tanggal' => $payment->tanggal_bayar ?? $payment->created_at,
            'tipe' => 'kredit',
            'nominal' => (float) ($payment->jumlah ?? 0),
            'keterangan' => 'Pembayaran ' . ($payment->metode ?? '-'),
            'meta' => [
                'source' => 'payment_store',
            ],
        ]);

        // Check if billing is fully paid
        $totalPaidAfter = (float) Payment::where('billing_id', $billing->id)->sum('jumlah');
        $totalPaidAfter = round($totalPaidAfter, 0);
        $totalTagihanAfter = round((float) $billing->total_tagihan, 0);
        if ($totalPaidAfter >= $totalTagihanAfter) {
            $billing->update(['status' => 'lunas']);
        } else {
            $billing->update(['status' => 'sebagian']);
        }

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Pembayaran berhasil dicatat');
    }
}

