@extends('layouts.app')

@section('title', 'Daftar Penyewaan')

@section('content_header')
    <h1>Daftar Penyewaan / Occupancies</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('occupancies.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Penyewaan
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($occupancies->isEmpty())
                <div class="alert alert-info">Belum ada data penyewaan.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kamar</th>
                            <th>Penyewa</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($occupancies as $i => $occ)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $occ->room->nomor_kamar ?? '-' }}</td>
                            <td>{{ $occ->consumer->nama ?? '-' }}</td>
                            <td>{{ $occ->tanggal_masuk }}</td>
                            <td>{{ $occ->tanggal_keluar ?? '-' }}</td>
                            <td>{{ $occ->status }}</td>
                            <td>
                                <a href="{{ route('occupancies.edit', $occ->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('occupancies.destroy', $occ->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
