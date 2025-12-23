<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Room;
use App\Models\Consumer;
use App\Models\Billing;
use App\Http\Requests\StoreRoomOccupancyRequest;
use App\Http\Requests\UpdateRoomOccupancyRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoomOccupancyController extends Controller
{
    public function index(Request $request)
    {
        // base query
        $occupancyQuery = RoomOccupancy::with('room','consumer')
            ->where('status', '!=', 'tidak aktif')
            ->orderBy('room_id')
            ->orderBy('id');

        // search by room number or tenant name
        if ($request->filled('q')) {
            $q = $request->get('q');
            $occupancyQuery->where(function($wrap) use ($q) {
                $wrap->whereHas('room', function($sub) use ($q) {
                        $sub->where('nomor_kamar', 'like', "%{$q}%");
                    })
                    ->orWhereHas('consumer', function($sub) use ($q) {
                        $sub->where('nama', 'like', "%{$q}%");
                    });
            });
        }

        // paginate occupancies (show many cards per page)
        $occupancies = $occupancyQuery->paginate(50)->withQueryString();

        // list available rooms (not occupied)
        $availableRoomsQuery = Room::where('status', 'tersedia')->orderBy('nomor_kamar');
        if ($request->filled('q')) {
            $q = $request->get('q');
            $availableRoomsQuery->where(function($sub) use ($q) {
                $sub->where('nomor_kamar', 'like', "%{$q}%")
                    ->orWhere('jenis_kamar', 'like', "%{$q}%");
            });
        }
        $availableRooms = $availableRoomsQuery->get();

        $today = Carbon::today();

        foreach ($occupancies as $occ) {
            // normalize tanggal_keluar
            if (empty($occ->tanggal_keluar)) {
                $occ->days_remaining = null;
                continue;
            }

            $tglKeluar = Carbon::parse($occ->tanggal_keluar);
            $occ->days_remaining = $tglKeluar->isPast() ? 0 : Carbon::today()->diffInDays($tglKeluar);

            // If checkout date has passed -> mark occupancy tidak aktif and set room available
            if ($tglKeluar->lt($today)) {
                if ($occ->status !== 'tidak aktif') {
                    $occ->update(['status' => 'tidak aktif']);
                }

                if ($occ->room && $occ->room->status !== 'tersedia') {
                    $occ->room->update(['status' => 'tersedia']);
                }

                // flag for view
                $occ->expired = true;
                continue;
            }

            // if within 5 days until checkout mark due_soon (yellow) and due_soon_unpaid when unpaid exists
            $daysUntil = $today->diffInDays($tglKeluar);
            if ($daysUntil <= 5) {
                $occ->due_soon = true;

                $hasUnpaid = Billing::where('room_id', $occ->room_id)
                    ->where('consumer_id', $occ->consumer_id)
                    ->whereIn('status', ['pending', 'sebagian'])
                    ->exists();

                if ($hasUnpaid) {
                    $occ->due_soon_unpaid = true;
                }
            }

            // attach latest billing (prefer unpaid/partial)
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
                
                // Calculate remaining amount
                $totalPaid = \App\Models\Payment::where('billing_id', $billing->id)->sum('jumlah');
                $occ->billing_remaining = $billing->total_tagihan - $totalPaid;
            } else {
                $occ->billing_id = null;
                $occ->billing_url = null;
                $occ->billing_status = null;
                $occ->billing_invoice = null;
                $occ->billing_total = null;
                $occ->billing_remaining = null;
            }

            // complete/finish url (to edit occupancy) when needed
            $occ->complete_url = route('occupancies.complete', $occ);
        }

        return view('occupancies.index', compact('occupancies', 'availableRooms'));
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
        $rooms = Room::orderBy('nomor_kamar')->get();
        $consumers = Consumer::orderBy('nama')->get();
        return view('occupancies.edit', compact('occupancy','rooms','consumers'));
    }

    public function update(UpdateRoomOccupancyRequest $request, RoomOccupancy $occupancy)
    {
        $data = $request->validated();
        $occupancy->update($data);

        // if status changed or room changed, ensure room statuses adjusted
        if (isset($data['room_id'])) {
            // set new room to terisi
            $newRoom = Room::find($data['room_id']);
            if ($newRoom) {
                $newRoom->update(['status' => 'terisi']);
            }
        }

        return redirect()->route('occupancies.index')->with('success','Data occupancy berhasil diperbarui');
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
