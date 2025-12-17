<?php

namespace App\Http\Controllers;

use App\Models\RoomAddon;
use App\Http\Requests\StoreRoomAddonRequest;
use App\Http\Requests\UpdateRoomAddonRequest;

class RoomAddonController extends Controller
{
    public function index()
    {
        $addons = RoomAddon::orderBy('nama_addon')->get();
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
