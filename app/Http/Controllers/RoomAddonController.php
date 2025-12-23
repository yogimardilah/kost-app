<?php

namespace App\Http\Controllers;

use App\Models\RoomAddon;
use App\Http\Requests\StoreRoomAddonRequest;
use App\Http\Requests\UpdateRoomAddonRequest;
use Illuminate\Http\Request;

class RoomAddonController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomAddon::orderBy('nama_addon');

        // Search functionality
        if ($request->filled('q')) {
            $searchTerm = $request->get('q');
            $query->where('nama_addon', 'like', '%' . $searchTerm . '%')
                  ->orWhere('satuan', 'like', '%' . $searchTerm . '%');
        }

        $addons = $query->paginate(10);
        return view('addons.index', compact('addons'));
    }

    public function create()
    {
        return view('addons.create');
    }

    public function store(StoreRoomAddonRequest $request)
    {
        RoomAddon::create($request->validated());
        return redirect()->route('addons.index')->with('success', 'Addon berhasil ditambahkan');
    }

    public function edit(RoomAddon $addon)
    {
        return view('addons.edit', compact('addon'));
    }

    public function update(UpdateRoomAddonRequest $request, RoomAddon $addon)
    {
        $addon->update($request->validated());
        return redirect()->route('addons.index')->with('success', 'Addon berhasil diperbarui');
    }

    public function destroy(RoomAddon $addon)
    {
        $addon->delete();
        return redirect()->route('addons.index')->with('success', 'Addon berhasil dihapus');
    }
}
