<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    public function index()
    {
        $kosts = Kost::orderBy('id', 'desc')->get();
        return view('kost.index', compact('kosts'));
    }

    public function create()
    {
        return view('kost.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kost'    => 'required',
            'alamat'       => 'required',
            'harga'        => 'required|numeric',
            'jumlah_kamar' => 'required|numeric',
            'status'       => 'required',
        ]);

          Kost::create([
            'nama_kost'    => $request->nama_kost,
            'alamat'       => $request->alamat,
            'pemilik_id'   => auth()->id(), // 🔥 INI KUNCI
            'harga'        => $request->harga,
            'jumlah_kamar' => $request->jumlah_kamar,
            'status'       => $request->status,
        ]);

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil ditambahkan');
    }

    public function edit(Kost $kost)
    {
        return view('kost.edit', compact('kost'));
    }

    public function update(Request $request, Kost $kost)
    {
        $request->validate([
            'nama_kost'    => 'required',
            'alamat'       => 'required',
            'harga'        => 'required|numeric',
            'jumlah_kamar' => 'required|numeric',
            'status'       => 'required',
        ]);

        $kost->update($request->all());

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil diupdate');
    }

    public function destroy(Kost $kost)
    {
        $kost->delete();

        return redirect()->route('kost.index')
            ->with('success', 'Data kost berhasil dihapus');
    }
}
