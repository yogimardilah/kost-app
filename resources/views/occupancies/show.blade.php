@extends('layouts.app')

@section('title', 'Detail Penyewaan')

@section('content_header')
    <h1>Detail Penyewaan</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr><th>Kamar</th><td>{{ $occupancy->room->nomor_kamar ?? '-' }}</td></tr>
                <tr><th>Penyewa</th><td>{{ $occupancy->consumer->nama ?? '-' }}</td></tr>
                <tr><th>Tanggal Masuk</th><td>{{ $occupancy->tanggal_masuk }}</td></tr>
                <tr><th>Tanggal Keluar</th><td>{{ $occupancy->tanggal_keluar ?? '-' }}</td></tr>
                <tr><th>Status</th><td>{{ $occupancy->status }}</td></tr>
            </table>
            <a href="{{ route('occupancies.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
