<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Room;
use App\Models\Consumer;
use App\Http\Requests\StoreRoomOccupancyRequest;
use App\Http\Requests\UpdateRoomOccupancyRequest;

class RoomOccupancyController extends Controller
{
    public function index()
    {
        $occupancies = RoomOccupancy::with('room','consumer')->orderBy('id','desc')->get();
        return view('occupancies.index', compact('occupancies'));
    }

    public function create()
    {
        $rooms = Room::where('status','tersedia')->orderBy('nomor_kamar')->get();
        $consumers = Consumer::orderBy('nama')->get();
        return view('occupancies.create', compact('rooms','consumers'));
    }

    public function store(StoreRoomOccupancyRequest $request)
    {
        $data = $request->validated();
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
}
