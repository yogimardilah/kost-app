<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use App\Http\Requests\StoreConsumerRequest;
use App\Http\Requests\UpdateConsumerRequest;

class ConsumerController extends Controller
{
    public function index()
    {
        $consumers = Consumer::orderBy('id', 'desc')->get();
        return view('consumers.index', compact('consumers'));
    }

    public function create()
    {
        return view('consumers.create');
    }

    public function store(StoreConsumerRequest $request)
    {
        Consumer::create($request->validated());
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil ditambahkan');
    }

    public function edit(Consumer $consumer)
    {
        return view('consumers.edit', compact('consumer'));
    }

    public function update(UpdateConsumerRequest $request, Consumer $consumer)
    {
        $consumer->update($request->validated());
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil diperbarui');
    }

    public function destroy(Consumer $consumer)
    {
        $consumer->delete();
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil dihapus');
    }
}
