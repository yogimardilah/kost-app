<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Kost;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index($kostId)
    {
        $rooms = Room::where('kost_id', $kostId)->with('addons','occupancies')->get();
        return view('rooms.index', compact('rooms','kostId'));
    }

    public function create($kostId)
    {
        return view('rooms.create', compact('kostId'));
    }

    public function store(Request $request)
    {
        Room::create($request->all());
        return redirect()->back()->with('success','Kamar berhasil ditambahkan');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $room->update($request->all());
        return redirect()->back()->with('success','Kamar berhasil diperbarui');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->back()->with('success','Kamar berhasil dihapus');
    }
}
