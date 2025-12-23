<?php

namespace App\Http\Controllers;

use App\Models\Consumer;
use App\Http\Requests\StoreConsumerRequest;
use App\Http\Requests\UpdateConsumerRequest;

class ConsumerController extends Controller
{
    public function index()
    {
        $search = request('search');
        
        $consumers = Consumer::query()
            ->when($search, function($query) use ($search) {
                $query->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('no_hp', 'LIKE', "%{$search}%")
                      ->orWhere('kendaraan', 'LIKE', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        return view('consumers.index', compact('consumers'));
    }

    public function create()
    {
        return view('consumers.create');
    }

    public function store(StoreConsumerRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('tanda_pengenal')) {
            $data['tanda_pengenal'] = $request->file('tanda_pengenal')->store('tanda_pengenal', 'public');
        }
        
        Consumer::create($data);
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil ditambahkan');
    }

    public function edit(Consumer $consumer)
    {
        return view('consumers.edit', compact('consumer'));
    }

    public function update(UpdateConsumerRequest $request, Consumer $consumer)
    {
        $data = $request->validated();
        
        if ($request->hasFile('tanda_pengenal')) {
            // Delete old file if exists
            if ($consumer->tanda_pengenal && \Storage::disk('public')->exists($consumer->tanda_pengenal)) {
                \Storage::disk('public')->delete($consumer->tanda_pengenal);
            }
            $data['tanda_pengenal'] = $request->file('tanda_pengenal')->store('tanda_pengenal', 'public');
        }
        
        $consumer->update($data);
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil diperbarui');
    }

    public function destroy(Consumer $consumer)
    {
        // Delete file if exists
        if ($consumer->tanda_pengenal && \Storage::disk('public')->exists($consumer->tanda_pengenal)) {
            \Storage::disk('public')->delete($consumer->tanda_pengenal);
        }
        
        $consumer->delete();
        return redirect()->route('consumers.index')->with('success', 'Penyewa berhasil dihapus');
    }
}
