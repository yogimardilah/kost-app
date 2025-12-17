<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use Illuminate\Http\Request;

class ConsumerController extends Controller
{
    public function index()
    {
        $consumers = Consumer::all();
        return view('consumers.index', compact('consumers'));
    }

    public function create()
    {
        return view('consumers.create');
    }

    public function store(Request $request)
    {
        Consumer::create($request->all());
        return redirect()->back()->with('success','Penyewa berhasil ditambahkan');
    }

    public function edit(Consumer $consumer)
    {
        return view('consumers.edit', compact('consumer'));
    }

    public function update(Request $request, Consumer $consumer)
    {
        $consumer->update($request->all());
        return redirect()->back()->with('success','Penyewa berhasil diperbarui');
    }

    public function destroy(Consumer $consumer)
    {
        $consumer->delete();
        return redirect()->back()->with('success','Penyewa berhasil dihapus');
    }
}
