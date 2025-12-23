<?php

namespace App\Http\Controllers;

use App\Models\AddonTransaction;
use App\Models\AddonTransactionDetail;
use App\Models\RoomAddon;
use App\Models\Consumer;
use App\Models\Room;
use App\Models\Billing;
use App\Models\BillingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddonTransactionController extends Controller
{
    public function index(Request $request)
    {
        // Show billing list (latest first) so addon and payment info stay in one place
        $query = Billing::with(['consumer', 'room'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhereHas('consumer', function ($c) use ($s) {
                        $c->where('nama', 'like', "%{$s}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $billings = $query->paginate(15)->withQueryString();

        // Precompute paid/remaining for the page to avoid N+1
        $billingIds = $billings->pluck('id');
        $paidMap = \App\Models\Payment::whereIn('billing_id', $billingIds)
            ->selectRaw('billing_id, SUM(jumlah) as total_paid')
            ->groupBy('billing_id')
            ->pluck('total_paid', 'billing_id');

        // Attach computed values
        $billings->getCollection()->transform(function ($b) use ($paidMap) {
            $paid = (float)($paidMap[$b->id] ?? 0);
            $b->total_paid = $paid;
            $b->remaining = max(0, (float)$b->total_tagihan - $paid);
            return $b;
        });

        return view('addon_transactions.index', compact('billings'));
    }

    public function create(Request $request)
    {
        $consumers = Consumer::orderBy('nama')->get();
        $rooms = Room::orderBy('nomor_kamar')->get();
        $addons = RoomAddon::orderBy('nama_addon')->get();
        
        // Pre-select consumer if provided
        $selectedConsumerId = $request->get('consumer_id');
        
        return view('addon_transactions.create', compact('consumers','rooms','addons','selectedConsumerId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consumer_id' => 'required|exists:consumers,id',
            'items' => 'required|array|min:1',
            'items.*.addon_id' => 'required|exists:room_addons,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
        ]);

        $billing = null;
        
        try {
            DB::transaction(function() use ($request, &$billing) {
                // Find existing billing for this consumer
                $billing = Billing::where('consumer_id', $request->consumer_id)
                    ->whereIn('status', ['pending', 'sebagian', 'lunas'])
                    ->orderByDesc('id')
                    ->first();

                if (!$billing) {
                    throw new \Exception('Tidak ditemukan Billing untuk penyewa ini. Pastikan penyewa sudah memiliki Billing aktif.');
                }

                $total = (float)$billing->total_tagihan;
                
                // Add addon items directly to billing
                foreach ($request->items as $item) {
                    $addon = RoomAddon::find($item['addon_id']);
                    $qty = (int)$item['qty'];
                    $harga = (float)$item['harga'];
                    $subtotal = $qty * $harga;
                    
                    BillingDetail::create([
                        'billing_id' => $billing->id,
                        'keterangan' => $addon->nama_addon,
                        'qty' => $qty,
                        'harga' => $harga,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }

                // Update status to pending if was lunas
                $newStatus = $billing->status;
                if ($billing->status === 'lunas') {
                    $newStatus = 'sebagian';
                }
                
                $billing->update([
                    'total_tagihan' => $total,
                    'status' => $newStatus,
                ]);
            });

            return redirect()->route('billings.show', $billing->id)->with('success', 'Addon berhasil ditambahkan ke Billing.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan addon: ' . $e->getMessage());
        }
    }

    public function show(AddonTransaction $addon_transaction)
    {
        $addon_transaction->load(['consumer','room','details']);
        return view('addon_transactions.show', ['trx' => $addon_transaction]);
    }

    public function consumerActiveRoom(\App\Models\Consumer $consumer)
    {
        // Find active billing for this consumer
        $billing = Billing::where('consumer_id', $consumer->id)
            ->whereIn('status', ['pending', 'sebagian', 'lunas'])
            ->orderByDesc('id')
            ->first();

        if (!$billing) {
            return response()->json([
                'billing' => null,
                'error' => 'Tidak ada Billing aktif'
            ]);
        }

        return response()->json([
            'billing' => [
                'id' => $billing->id,
                'invoice_number' => $billing->invoice_number,
                'total_tagihan' => $billing->total_tagihan,
                'status' => $billing->status,
            ],
        ]);
    }

    public function postToBilling(AddonTransaction $addon_transaction)
    {
        if ($addon_transaction->status !== 'pending') {
            return back()->with('error','Transaksi sudah diproses.');
        }

        try {
            DB::transaction(function() use ($addon_transaction) {
                // Try to find existing billing for this consumer
                // First try: same room
                $existingBilling = null;
                
                if ($addon_transaction->room_id) {
                    $existingBilling = Billing::where('consumer_id', $addon_transaction->consumer_id)
                        ->where('room_id', $addon_transaction->room_id)
                        ->whereIn('status', ['pending', 'sebagian', 'lunas'])
                        ->orderByDesc('id')
                        ->first();
                }
                
                // Second try: any billing from same consumer
                if (!$existingBilling) {
                    $existingBilling = Billing::where('consumer_id', $addon_transaction->consumer_id)
                        ->whereIn('status', ['pending', 'sebagian', 'lunas'])
                        ->orderByDesc('id')
                        ->first();
                }

                // Third try: find from active occupancy
                if (!$existingBilling) {
                    $occupancy = RoomOccupancy::where('consumer_id', $addon_transaction->consumer_id)
                        ->whereNotIn('status', ['tidak aktif'])
                        ->orderByDesc('id')
                        ->first();
                    
                    if ($occupancy) {
                        $existingBilling = Billing::where('consumer_id', $addon_transaction->consumer_id)
                            ->where('room_id', $occupancy->room_id)
                            ->orderByDesc('id')
                            ->first();
                    }
                }

                if ($existingBilling) {
                    // Add addon details to existing billing
                    $billing = $existingBilling;
                    $total = (float)$billing->total_tagihan;
                    
                    foreach ($addon_transaction->details as $d) {
                        BillingDetail::create([
                            'billing_id' => $billing->id,
                            'keterangan' => $d->nama_addon,
                            'qty' => $d->qty,
                            'harga' => $d->harga,
                            'subtotal' => $d->subtotal,
                        ]);
                        $total += (float)$d->subtotal;
                    }
                    
                    // Update status to pending if was lunas
                    $newStatus = $billing->status;
                    if ($billing->status === 'lunas') {
                        $newStatus = 'sebagian';
                    }
                    
                    $billing->update([
                        'total_tagihan' => $total,
                        'status' => $newStatus,
                    ]);
                } else {
                    // No existing billing found - show error
                    throw new \Exception('Tidak ditemukan Billing untuk penyewa ini. Pastikan penyewa sudah memiliki Occupancy/Billing aktif.');
                }

                // Update addon transaction status to posted
                $addon_transaction->update(['status' => 'posted']);
            });

            return redirect()->route('addon-transactions.index')
                ->with('success', 'Addon berhasil diposting ke Billing.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal posting: ' . $e->getMessage());
        }
    }

    protected function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $count = AddonTransaction::whereDate('created_at', today())->count() + 1;
        return sprintf('AT-%s-%05d', $date, $count);
    }

    protected function generateBillingNumber(): string
    {
        $date = now()->format('Ymd');
        $count = Billing::whereDate('created_at', today())->count() + 1;
        return sprintf('INV-%s-%05d', $date, $count);
    }
}
