<?php

namespace App\Http\Controllers;

use App\Models\RoomAddon;
use Illuminate\Http\Request;

class RoomAddonController extends Controller
{
    public function index()
    {
        $addons = RoomAddon::all();
        return view('addons.index', compact('addons'));
    }

    public function create()
    {
        return view('addons.create');
    }

    public function store(Request $request)
    {
        RoomAddon::create($request->all());
        return redirect()->back()->with('success','Addon berhasil ditambahkan');
    }

    public function edit(RoomAddon $addon)
    {
        return view('addons.edit', compact('addon'));
    }

    public function update(Request $request, RoomAddon $addon)
    {
        $addon->update($request->all());
        return redirect()->back()->with('success','Addon berhasil diperbarui');
    }

    public function destroy(RoomAddon $addon)
    {
        $addon->delete();
        return redirect()->back()->with('success','Addon berhasil dihapus');
    }
}
