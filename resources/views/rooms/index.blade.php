@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Kamar Kost</h2>
    <a href="{{ route('rooms.create', $kostId) }}" class="btn btn-primary mb-2">Tambah Kamar</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tipe</th>
                <th>Harga/Bulan</th>
                <th>Status</th>
                <th>Addon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rooms as $room)
            <tr>
                <td>{{ $room->kode_kamar }}</td>
                <td>{{ $room->tipe_kamar }}</td>
                <td>{{ $room->harga_bulanan }}</td>
                <td>{{ $room->status_kamar }}</td>
                <td>
                    @foreach($room->addons as $addon)
                        <span class="badge bg-info">{{ $addon->nama_addon }}</span>
                    @endforeach
                </td>
                <td>
                    <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kamar ini?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
