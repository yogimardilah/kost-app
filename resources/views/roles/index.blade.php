@extends('layouts.app')

@section('title', 'Daftar Role')

@section('content_header')
    <h1>Daftar Role</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Role
            </a>
        </div>
        <div class="card-body">
            @if($roles->isEmpty())
                <div class="alert alert-info">Belum ada role.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $idx => $role)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus role ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
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
