<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;

class KostController extends Controller
{
    public function index(Request $request)
    {
        $query = Kost::orderBy('id', 'desc');

        // Search functionality
        if ($request->filled('q')) {
            $searchTerm = $request->get('q');
            $query->where('nama_kost', 'like', '%' . $searchTerm . '%')
                  ->orWhere('alamat', 'like', '%' . $searchTerm . '%')
                  ->orWhere('kota', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('telepon', 'like', '%' . $searchTerm . '%');
        }

        $kosts = $query->paginate(10);
        return view('kost.index', compact('kosts'));
    }

    public function create()
    {
        return view('kost.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kost' => 'required|string|max:255',
            'alamat'    => 'required|string',
            'kota'      => 'nullable|string|max:100',
            'provinsi'  => 'nullable|string|max:100',
            'telepon'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        Kost::create($request->only([
            'nama_kost',
            'alamat',
            'kota',
            'provinsi',
            'telepon',
            'email',
            'deskripsi',
        ]));

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
            'nama_kost' => 'required|string|max:255',
            'alamat'    => 'required|string',
            'kota'      => 'nullable|string|max:100',
            'provinsi'  => 'nullable|string|max:100',
            'telepon'   => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $kost->update($request->only([
            'nama_kost',
            'alamat',
            'kota',
            'provinsi',
            'telepon',
            'email',
            'deskripsi',
        ]));

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
