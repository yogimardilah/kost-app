@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content_header')
    <h1>Daftar Pengguna</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if($users->isEmpty())
                <div class="alert alert-info">Belum ada pengguna.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $idx => $user)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role)
                                    <span class="badge bg-primary">{{ $user->role->name ?? '-' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
