<?php

namespace App\Http\Controllers;

use App\Models\RoomOccupancy;
use App\Models\Room;
use App\Models\Consumer;
use Illuminate\Http\Request;

class RoomOccupancyController extends Controller
{
    public function index()
    {
        $occupancies = RoomOccupancy::with('room','consumer')->get();
        return view('occupancies.index', compact('occupancies'));
    }

    public function create()
    {
        $rooms = Room::where('status_kamar','tersedia')->get();
        $consumers = Consumer::all();
        return view('occupancies.create', compact('rooms','consumers'));
    }

    public function store(Request $request)
    {
        RoomOccupancy::create($request->all());
        // Optional: ubah status kamar menjadi 'terisi'
        $room = Room::find($request->room_id);
        $room->update(['status_kamar'=>'terisi']);
        return redirect()->back()->with('success','Penyewa berhasil check-in');
    }

    public function edit(RoomOccupancy $occupancy)
    {
        $rooms = Room::all();
        $consumers = Consumer::all();
        return view('occupancies.edit', compact('occupancy','rooms','consumers'));
    }

    public function update(Request $request, RoomOccupancy $occupancy)
    {
        $occupancy->update($request->all());
        return redirect()->back()->with('success','Data occupancy berhasil diperbarui');
    }

    public function destroy(RoomOccupancy $occupancy)
    {
        $occupancy->delete();
        return redirect()->back()->with('success','Data occupancy berhasil dihapus');
    }
}
