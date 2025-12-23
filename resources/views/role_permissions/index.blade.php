@extends('layouts.app')

@section('title', 'Manajemen Role Permissions')

@section('content_header')
    <h1>Manajemen Role Permissions</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('role-permissions.reset') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-warning btn-sm" onclick="return confirm('Reset semua permissions ke default? Owner/Admin akan dapat akses penuh.')">
                    <i class="fas fa-sync"></i> Reset Ke Default
                </button>
            </form>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if($roles->isEmpty())
                <div class="alert alert-info">Belum ada role.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Role</th>
                                <th>Deskripsi</th>
                                <th style="width: 15%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td>
                                    @php
                                        $permCount = \App\Models\RolePermission::where('role_id', $role->id)->where(function($q) {
                                            $q->where('can_view', 1)
                                              ->orWhere('can_create', 1)
                                              ->orWhere('can_update', 1)
                                              ->orWhere('can_delete', 1);
                                        })->count();
                                    @endphp
                                    {{ $permCount }} menu(s) memiliki akses
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('role-permissions.edit', $role) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
