<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Billing;
use App\Http\Requests\StorePaymentRequest;

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
    public function create(Billing $billing = null)
    {
        $billings = Billing::where('status', '!=', 'lunas')
            ->with('consumer')
            ->orderBy('id', 'desc')
            ->get();
        return view('payments.create', compact('billings', 'billing'));
    }

    /**
     * Store a newly recorded payment.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();
        $payment = Payment::create($data);

        // Check if billing is fully paid
        $billing = Billing::find($data['billing_id']);
        if ($billing) {
            $totalPaid = Payment::where('billing_id', $billing->id)->sum('jumlah');
            if ($totalPaid >= $billing->total_tagihan) {
                $billing->update(['status' => 'lunas']);
            } else {
                $billing->update(['status' => 'sebagian']);
            }
        }

        return redirect()->route('billings.show', $billing)
            ->with('success', 'Pembayaran berhasil dicatat');
    }
}

