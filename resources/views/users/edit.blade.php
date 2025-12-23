@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content_header')
    <h1>Edit Pengguna</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label for="name">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="role_id">Role</label>
                    <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <hr>
                <p class="text-muted"><small>Kosongkan jika tidak ingin mengubah password</small></p>

                <div class="form-group mb-3">
                    <label for="password">Password Baru</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
