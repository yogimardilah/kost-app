@extends('adminlte::page')

@section('title', 'Tambah Kost')

@section('content')
<h3>Tambah Kost</h3>

<form action="{{ route('kost.store') }}" method="POST">
    @csrf

    <div class="form-group">
        <label>Nama Kost</label>
        <input type="text" name="nama_kost" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Alamat</label>
        <textarea name="alamat" class="form-control" required></textarea>
    </div>

    <div class="form-group">
        <label>Harga</label>
        <input type="number" name="harga" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Jumlah Kamar</label>
        <input type="number" name="jumlah_kamar" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="tersedia">Tersedia</option>
            <option value="penuh">Penuh</option>
        </select>
    </div>

    <button class="btn btn-success">Simpan</button>
    <a href="{{ route('kost.index') }}" class="btn btn-secondary">Kembali</a>
</form>
@stop
