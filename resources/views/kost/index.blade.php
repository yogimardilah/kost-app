@extends('adminlte::page')

@section('title', 'Data Kost')

@section('content_header')
    <h1>Data Kost</h1>
@stop

@section('content')

<a href="{{ route('kost.create') }}" class="btn btn-primary mb-3">
    + Tambah Kost
</a>

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kost</th>
            <th>Alamat</th>
            <th>Harga</th>
            <th>Kamar</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($kosts as $no => $kost)
        <tr>
            <td>{{ $no + 1 }}</td>
            <td>{{ $kost->nama_kost }}</td>
            <td>{{ $kost->alamat }}</td>
            <td>Rp {{ number_format($kost->harga) }}</td>
            <td>{{ $kost->jumlah_kamar }}</td>
            <td>{{ $kost->status }}</td>
            <td>
                <a href="{{ route('kost.edit', $kost->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('kost.destroy', $kost->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm"
                        onclick="return confirm('Yakin hapus?')">
                        Hapus
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@stop
