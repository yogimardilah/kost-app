<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Room;
use App\Models\Consumer;
use App\Models\Billing;
use App\Models\BillingDetail;
use App\Models\Payment;
use App\Http\Requests\StoreRoomOccupancyRequest;
use App\Http\Requests\UpdateRoomOccupancyRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomOccupancyController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Get all rooms ordered by ID (without search filter at this level)
        $allRooms = Room::orderBy('id')->get();

        // Build full occupancy list with all data
        $occupancies = [];
        $availableRooms = [];

        foreach ($allRooms as $room) {
            // Get active occupancy for this room
            $occ = RoomOccupancy::with('consumer')
                ->where('room_id', $room->id)
                ->where('status', '!=', 'tidak aktif')
                ->first();

            if ($occ) {
                // Handle occupancy
                if (empty($occ->tanggal_keluar)) {
                    $occ->days_remaining = null;
                } else {
                    $tglKeluar = Carbon::parse($occ->tanggal_keluar);
                    $occ->days_remaining = $tglKeluar->isPast() ? 0 : $today->diffInDays($tglKeluar);

                    if ($tglKeluar->lt($today)) {
                        // Update only fillable fields
                        RoomOccupancy::where('id', $occ->id)->update(['status' => 'tidak aktif']);
                        $room->update(['status' => 'tersedia']);
                        $availableRooms[] = $room;
                        continue;
                    }

                    // Check if within 5 days
                    $daysUntil = $today->diffInDays($tglKeluar);
                    if ($daysUntil <= 5) {
                        $occ->due_soon = true;
                    }
                }

                // Check for unpaid bills (regardless of checkout date)
                $hasUnpaid = Billing::where('room_id', $occ->room_id)
                    ->where('consumer_id', $occ->consumer_id)
                    ->whereIn('status', ['pending', 'sebagian'])
                    ->exists();

                if ($hasUnpaid) {
                    $occ->has_unpaid = true;
                }

                // Attach room relation
                $occ->room = $room;

                // Attach billing info
                $billing = Billing::where('room_id', $occ->room_id)
                    ->where('consumer_id', $occ->consumer_id)
                    ->whereIn('status', ['pending', 'sebagian'])
                    ->orderByDesc('id')
                    ->first();

                if (!$billing) {
                    $billing = Billing::where('room_id', $occ->room_id)
                        ->where('consumer_id', $occ->consumer_id)
                        ->orderByDesc('id')
                        ->first();
                }

                if ($billing) {
                    $occ->billing_id = $billing->id;
                    $occ->billing_url = route('payments.create', ['billing' => $billing->id]);
                    $occ->billing_status = $billing->status;
                    $occ->billing_invoice = $billing->invoice_number;
                    $occ->billing_total = $billing->total_tagihan;
                    
                    $totalPaid = \App\Models\Payment::where('billing_id', $billing->id)->sum('jumlah');
                    $occ->billing_remaining = $billing->total_tagihan - $totalPaid;
                }

                // Always set complete URL - button will show when billing is paid/none
                $occ->complete_url = route('occupancies.complete', $occ->id);
                // Upgrade URL for owner-only action
                $occ->upgrade_url = route('occupancies.upgrade', $occ->id);

                $occupancies[] = $occ;
            } else {
                // Room is available
                if ($room->status === 'tersedia') {
                    $availableRooms[] = $room;
                }
            }
        }

        // Combine occupied and available rooms, ordered by room ID
        $allRoomsList = [];
        
        // Create lookup for occupancies
        $occupancyByRoomId = [];
        foreach ($occupancies as $occ) {
            $occupancyByRoomId[$occ->room_id] = $occ;
        }
        
        // Iterate through all rooms in order and build combined list
        foreach ($allRooms as $room) {
            if (isset($occupancyByRoomId[$room->id])) {
                $allRoomsList[] = $occupancyByRoomId[$room->id];
            } else if ($room->status === 'tersedia') {
                // Create a pseudo-occupancy object for available rooms
                $available = (object) [
                    'id' => null,
                    'room_id' => $room->id,
                    'room' => $room,
                    'consumer' => null,
                    'status' => 'available',
                    'tanggal_masuk' => null,
                    'tanggal_keluar' => null,
                ];
                $allRoomsList[] = $available;
            }
        }

        // Apply search filter to combined list
        if ($request->filled('q')) {
            $q = $request->get('q');
            $allRoomsList = array_filter($allRoomsList, function($item) use ($q) {
                if ($item->status === 'available') {
                    return (stripos($item->room->nomor_kamar, $q) !== false ||
                            stripos($item->room->jenis_kamar, $q) !== false);
                } else {
                    return (stripos($item->room->nomor_kamar, $q) !== false ||
                            stripos($item->consumer->nama ?? '', $q) !== false);
                }
            });
            $allRoomsList = array_values($allRoomsList);
        }

        // Convert to paginated collection
        $page = request()->get('page', 1);
        $perPage = (int)request()->get('per_page', 100);
        $offset = ($page - 1) * $perPage;
        $paginatedOccupancies = new \Illuminate\Pagination\LengthAwarePaginator(
            array_slice($allRoomsList, $offset, $perPage),
            count($allRoomsList),
            $perPage,
            $page,
            [
                'path' => route('occupancies.index'),
                'query' => $request->query(),
            ]
        );
        
        // Separate for view (occupied vs available from paginated list)
        $occupancies = [];
        $availableRooms = [];
        foreach ($paginatedOccupancies as $item) {
            if ($item->status === 'available') {
                $availableRooms[] = $item->room;
            } else {
                $occupancies[] = $item;
            }
        }
        
        return view('occupancies.index', compact('paginatedOccupancies', 'occupancies', 'availableRooms'));
    }

    public function create(Request $request)
    {
        $rooms = Room::where('status','tersedia')->orderBy('nomor_kamar')->get();
        $selectedRoomId = $request->get('room_id');
        $consumers = Consumer::whereDoesntHave('occupancies', function ($q) {
            $q->where('status', 'aktif');
        })->orderBy('nama')->get();
        return view('occupancies.create', compact('rooms','consumers','selectedRoomId'));
    }

    public function store(StoreRoomOccupancyRequest $request)
    {
        $data = $request->validated();
        // Determine default checkout date for monthly rentals
        if (($data['tipe_sewa'] ?? null) === 'bulanan' && empty($data['tanggal_keluar'])) {
            $masuk = Carbon::parse($data['tanggal_masuk']);
            $data['tanggal_keluar'] = $masuk->copy()->addDays(30)->toDateString();
        }
        // Determine default check-in date if user provided checkout for monthly rentals
        if (($data['tipe_sewa'] ?? null) === 'bulanan' && empty($data['tanggal_masuk']) && !empty($data['tanggal_keluar'])) {
            $keluar = Carbon::parse($data['tanggal_keluar']);
            $data['tanggal_masuk'] = $keluar->copy()->subDays(30)->toDateString();
        }
        // Remove non-persisted field before create
        unset($data['tipe_sewa']);
        
        // Set default status to 'aktif' if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'aktif';
        }

        $occupancy = RoomOccupancy::create($data);

        // update room status to terisi
        $room = Room::find($data['room_id']);
        if ($room) {
            $room->update(['status' => 'terisi']);
        }

        // Auto-generate billing for this occupancy
        \App\Services\BillingService::generateBillingForOccupancy($occupancy);

        return redirect()->route('occupancies.index')->with('success','Penyewa berhasil check-in dan tagihan telah dibuat');
    }

    public function edit(RoomOccupancy $occupancy)
    {
        // Only owner (role_id = 1) can edit
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data penyewaan');
        }

        $rooms = Room::orderBy('nomor_kamar')->get();
        $consumers = Consumer::orderBy('nama')->get();
        return view('occupancies.edit', compact('occupancy','rooms','consumers'));
    }

    public function update(UpdateRoomOccupancyRequest $request, RoomOccupancy $occupancy)
    {
        // Only owner (role_id = 1) can update
        if (auth()->user()->role_id !== 1) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data penyewaan');
        }

        $data = $request->validated();

        // Prevent room change via general edit; use dedicated upgrade flow
        if ($data['room_id'] != $occupancy->room_id) {
            return back()->withErrors(['room_id' => 'Ganti kamar lewat menu upgrade, bukan edit.'])->withInput();
        }

        $occupancy->update($data);

        return redirect()->route('occupancies.index')->with('success','Data occupancy berhasil diperbarui');
    }

    public function upgradeForm(RoomOccupancy $occupancy)
    {
        // Allow Owner (1) and Admin (2) to access upgrade
        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Anda tidak memiliki akses untuk upgrade kamar');
        }

        $occupancy->loadMissing('room', 'consumer');

        $billing = Billing::where('room_id', $occupancy->room_id)
            ->where('consumer_id', $occupancy->consumer_id)
            ->whereIn('status', ['pending', 'sebagian'])
            ->orderByDesc('id')
            ->first();

        if (!$billing) {
            $billing = Billing::where('room_id', $occupancy->room_id)
                ->where('consumer_id', $occupancy->consumer_id)
                ->orderByDesc('id')
                ->first();
        }

        $rentType = '-';
        $billingSummary = null;
        if ($billing) {
            if ($billing->periode_awal && $billing->periode_akhir) {
                $days = max(1, Carbon::parse($billing->periode_awal)->diffInDays(Carbon::parse($billing->periode_akhir)));
                $rentType = $days < 30 ? 'Harian' : 'Bulanan';
            }
            $paid = Payment::where('billing_id', $billing->id)->sum('jumlah');
            $billingSummary = [
                'invoice' => $billing->invoice_number,
                'status' => $billing->status,
                'total' => $billing->total_tagihan,
                'paid' => $paid,
                'remaining' => $billing->total_tagihan - $paid,
            ];
        }

        $rooms = Room::where('status', 'tersedia')
            ->where('id', '!=', $occupancy->room_id)
            ->orderBy('nomor_kamar')
            ->get();

        return view('occupancies.upgrade', compact('occupancy', 'rooms', 'billing', 'rentType', 'billingSummary'));
    }

    public function applyUpgrade(Request $request, RoomOccupancy $occupancy)
    {
        // Allow Owner (1) and Admin (2) to perform upgrade
        if (!in_array(auth()->user()->role_id, [1, 2])) {
            abort(403, 'Anda tidak memiliki akses untuk upgrade kamar');
        }

        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'upgrade_from' => 'required|date',
            'upgrade_to' => 'required|date|after_or_equal:upgrade_from',
            'rent_type' => 'required|in:Bulanan,Harian',
        ]);

        $occupancy->loadMissing('room');
        $oldRoom = $occupancy->room;
        $newRoom = Room::find($data['room_id']);

        if (!$newRoom || $newRoom->id === $occupancy->room_id) {
            return back()->withErrors(['room_id' => 'Pilih kamar lain untuk upgrade.']);
        }

        // adjust billing: no new transaction, add delta to current or latest invoice
        $billing = Billing::where('consumer_id', $occupancy->consumer_id)
            ->whereIn('status', ['pending', 'sebagian'])
            ->latest()
            ->first();

        if (!$billing) {
            $billing = Billing::where('consumer_id', $occupancy->consumer_id)
                ->latest()
                ->first();
        }

        if (!$billing) {
            return back()->withErrors(['room_id' => 'Tidak ada invoice aktif untuk ditambahkan selisih.']);
        }

        // Use custom date range provided by user
        $upgradeFrom = Carbon::parse($data['upgrade_from']);
        $upgradeTo = Carbon::parse($data['upgrade_to']);
        $days = max(1, $upgradeFrom->diffInDays($upgradeTo));

        // Determine pricing based on current rent type
        if ($data['rent_type'] === 'Bulanan') {
            // Always use monthly logic for old room
            if ($days <= 30) {
                $oldTotal = $oldRoom->harga ?? 0;
            } else {
                $remainingDays = $days - 30;
                $oldTotal = ($oldRoom->harga ?? 0) + (($oldRoom->harga_harian ?? 0) * $remainingDays);
            }

            // Determine new room total
            if ($days <= 30) {
                $newTotal = $newRoom->harga ?? 0;
            } else {
                $remainingDays = $days - 30;
                $newTotal = ($newRoom->harga ?? 0) + (($newRoom->harga_harian ?? 0) * $remainingDays);
            }
        } else {
            // Harian: always use daily pricing
            $oldTotal = ($oldRoom->harga_harian ?? 0) * $days;
            $newTotal = ($newRoom->harga_harian ?? 0) * $days;
        }

        $delta = $newTotal - $oldTotal;

        if ($delta !== 0) {
            BillingDetail::create([
                'billing_id' => $billing->id,
                'keterangan' => 'Upgrade kamar dari ' . ($oldRoom->nomor_kamar ?? '-') . ' ke ' . ($newRoom->nomor_kamar ?? '-') . ' (' . $upgradeFrom->format('d/m/Y') . ' s/d ' . $upgradeTo->format('d/m/Y') . ')',
                'qty' => 1,
                'harga' => $delta,
                'subtotal' => $delta,
            ]);
            $billing->increment('total_tagihan', $delta);

            if ($billing->status === 'lunas') {
                $billing->status = 'sebagian';
            }
        }

        $billing->room_id = $newRoom->id;
        $billing->save();

        // update occupancy and room statuses
        $occupancy->update(['room_id' => $newRoom->id]);
        $newRoom->update(['status' => 'terisi']);
        if ($oldRoom) {
            $oldRoom->update(['status' => 'tersedia']);
        }

        return redirect()->route('occupancies.index')->with('success', 'Upgrade kamar berhasil, selisih ditagihkan di invoice.');
    }

    public function destroy(RoomOccupancy $occupancy)
    {
        $occupancy->delete();
        return redirect()->route('occupancies.index')->with('success','Data occupancy berhasil dihapus');
    }

    public function complete(RoomOccupancy $occupancy)
    {
        // set occupancy inactive
        $occupancy->update(['status' => 'tidak aktif']);

        // set room available
        if ($occupancy->room) {
            $occupancy->room->update(['status' => 'tersedia']);
        }

        return redirect()->route('occupancies.index')->with('success', 'Penyewaan diselesaikan, kamar kini tersedia');
    }
}
