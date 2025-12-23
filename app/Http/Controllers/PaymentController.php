<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Billing;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('billing')->orderBy('id','desc')->get();
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
        
        if ($request->filled('billing')) {
            $billing = Billing::with(['consumer', 'room', 'details'])->find($request->get('billing'));
            if ($billing) {
                $billingDetails = $billing->details;
                $totalPaid = Payment::where('billing_id', $billing->id)->sum('jumlah');
                $remaining = $billing->total_tagihan - $totalPaid;
            }
        }

        $billings = Billing::where('status', '!=', 'lunas')
            ->with('consumer')
            ->orderBy('id', 'desc')
            ->get();
        return view('payments.create', compact('billings', 'billing', 'billingDetails', 'totalPaid', 'remaining'));
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

        $totalPaid = Payment::where('billing_id', $billing->id)->sum('jumlah');
        $remaining = $billing->total_tagihan - $totalPaid;
        if ($data['jumlah'] > $remaining) {
            return back()
                ->withErrors(['jumlah' => 'Jumlah pembayaran melebihi sisa tagihan (sisa: Rp '.number_format($remaining,0,',','.').')'])
                ->withInput();
        }

        // handle file upload (optional)
        $note = $data['bukti_bayar'] ?? null;
        if ($request->hasFile('bukti_bayar_file')) {
            $path = $request->file('bukti_bayar_file')->store('payments', 'public');
            $data['bukti_bayar'] = $note ? ($note . ' | file:' . $path) : $path;
        }

        $payment = Payment::create($data);

        // Check if billing is fully paid
        $totalPaidAfter = Payment::where('billing_id', $billing->id)->sum('jumlah');
        if ($totalPaidAfter >= $billing->total_tagihan) {
            $billing->update(['status' => 'lunas']);
        } else {
            $billing->update(['status' => 'sebagian']);
        }

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Pembayaran berhasil dicatat');
    }
}

