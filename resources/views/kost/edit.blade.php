@extends('adminlte::page')

@section('title', 'Edit Kost')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Kost</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('kost.update', $kost->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_kost">Nama Kost <span class="text-danger">*</span></label>
                <input type="text" id="nama_kost" name="nama_kost" class="form-control @error('nama_kost') is-invalid @enderror"
                       value="{{ old('nama_kost', $kost->nama_kost) }}" required>
                @error('nama_kost')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="alamat">Alamat <span class="text-danger">*</span></label>
                <textarea id="alamat" name="alamat" class="form-control @error('alamat') is-invalid @enderror" 
                          rows="3" required>{{ old('alamat', $kost->alamat) }}</textarea>
                @error('alamat')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kota">Kota</label>
                        <input type="text" id="kota" name="kota" class="form-control @error('kota') is-invalid @enderror"
                               value="{{ old('kota', $kost->kota) }}">
                        @error('kota')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="provinsi">Provinsi</label>
                        <input type="text" id="provinsi" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror"
                               value="{{ old('provinsi', $kost->provinsi) }}">
                        @error('provinsi')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telepon">Telepon</label>
                        <input type="text" id="telepon" name="telepon" class="form-control @error('telepon') is-invalid @enderror"
                               value="{{ old('telepon', $kost->telepon) }}">
                        @error('telepon')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $kost->email) }}">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                          rows="3">{{ old('deskripsi', $kost->deskripsi) }}</textarea>
                @error('deskripsi')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('kost.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@stop
