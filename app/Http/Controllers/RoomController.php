<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Kost;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;

class RoomController extends Controller
{
    public function index(Request $request, $kostId = null)
    {
        $query = Room::with('addons', 'occupancies');

        if ($kostId) {
            $query->where('kost_id', $kostId);
        } elseif ($request->filled('kost_id')) {
            $query->where('kost_id', $request->get('kost_id'));
        }

        $rooms = $query->get();
        return view('rooms.index', compact('rooms','kostId'));
    }

    public function create($kostId = null)
    {
        $kosts = Kost::orderBy('nama_kost')->get();
        return view('rooms.create', compact('kosts', 'kostId'));
    }

    public function store(StoreRoomRequest $request)
    {
        Room::create($request->validated());
        return redirect()->route('rooms.index')->with('success','Kamar berhasil ditambahkan');
    }

    public function edit(Room $room)
    {
        $kosts = Kost::orderBy('nama_kost')->get();
        return view('rooms.edit', compact('room', 'kosts'));
    }

    public function update(UpdateRoomRequest $request, Room $room)
    {
        $room->update($request->validated());
        return redirect()->route('rooms.index')->with('success','Kamar berhasil diperbarui');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->back()->with('success','Kamar berhasil dihapus');
    }
}