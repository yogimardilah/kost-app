@extends('layouts.app')

@section('title', 'Daftar Pengguna')

@section('content_header')
    <h1>Daftar Pengguna</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah User
            </a>
            <form method="GET" action="{{ route('users.index') }}" class="form-inline">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="Cari nama atau email" value="{{ request('q') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i> Cari</button>
                        @if(request('q'))
                            <a href="{{ route('users.index') }}" class="btn btn-outline-danger ml-2"><i class="fas fa-times"></i> Reset</a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

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
                            <td>{{ ($users->currentPage() - 1) * $users->perPage() + $idx + 1 }}</td>
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

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
