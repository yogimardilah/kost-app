@extends('layouts.app')

@section('title', 'Daftar Kamar')

@section('content_header')
    <h1>Daftar Kamar</h1>
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('rooms.create') }}" class="btn btn-primary">Tambah Kamar</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor</th>
                        <th>Jenis</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $room)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $room->nomor_kamar }}</td>
                            <td>{{ $room->jenis_kamar }}</td>
                            <td>{{ number_format($room->harga,0,',','.') }}</td>
                            <td>
                                @if($room->status === 'tersedia')
                                    <span class="badge bg-success">Tersedia</span>
                                @else
                                    <span class="badge bg-danger">Terisi</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('rooms.edit', $room) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus kamar?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Belum ada kamar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
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
