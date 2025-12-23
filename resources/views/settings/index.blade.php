@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content_header')
    <h1>Pengaturan</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Pengaturan Umum</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama Aplikasi:</strong> Kost Management System</p>
                    <p><strong>Versi:</strong> 1.0.0</p>
                    <p><strong>Tanggal Update Terakhir:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Profil Pengguna</h5>
                </div>
                <div class="card-body">
                    <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                    <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                    <p><strong>Role:</strong> {{ auth()->user()->role->name ?? '-' }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profil</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <p><strong>PHP Version:</strong> {{ phpversion() }}</p>
                    <p><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                    <p><strong>Database:</strong> {{ config('database.default') }}</p>
                    <p><strong>Environment:</strong> {{ config('app.env') }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Aksi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-danger btn-sm">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
