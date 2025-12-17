@extends('adminlte::page')

@section('title', 'Edit Kost')

@section('content')
<h3>Edit Kost</h3>

<form action="{{ route('kost.update', $kost->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nama Kost</label>
        <input type="text" name="nama_kost" class="form-control"
               value="{{ $kost->nama_kost }}" required>
    </div>

    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required>{{ $kost->alamat }}</textarea>
    </div>

    <div class="form-group">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control"
               value="{{ $kost->harga }}" required>
    </div>

    <div class="form-group">
        <label>Jumlah Kamar</label>
        <input type="number" name="jumlah_kamar" class="form-control"
               value="{{ $kost->jumlah_kamar }}" required>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="tersedia" {{ $kost->status == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
            <option value="penuh" {{ $kost->status == 'penuh' ? 'selected' : '' }}>Penuh</option>
        </select>
    </div>

    <button class="btn btn-success">Update</button>
    <a href="{{ route('kost.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@stop
