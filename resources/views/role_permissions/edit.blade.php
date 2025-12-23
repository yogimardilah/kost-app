@extends('layouts.app')

@section('title', 'Edit Role Permissions - ' . $role->name)

@section('content_header')
    <h1>Edit Permissions untuk Role: <strong>{{ $role->name }}</strong></h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('role-permissions.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 25%;">Menu</th>
                                <th style="text-align: center;">View</th>
                                <th style="text-align: center;">Create</th>
                                <th style="text-align: center;">Update</th>
                                <th style="text-align: center;">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $code => $label)
                            @php
                                $perm = $permissions->get($code);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $label }}</strong>
                                    <small class="text-muted d-block">({{ $code }})</small>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="menu_{{ $code }}_view" 
                                        {{ $perm && $perm->can_view ? 'checked' : '' }} class="form-check-input">
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="menu_{{ $code }}_create" 
                                        {{ $perm && $perm->can_create ? 'checked' : '' }} class="form-check-input">
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="menu_{{ $code }}_update" 
                                        {{ $perm && $perm->can_update ? 'checked' : '' }} class="form-check-input">
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox" name="menu_{{ $code }}_delete" 
                                        {{ $perm && $perm->can_delete ? 'checked' : '' }} class="form-check-input">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Permissions
                    </button>
                    <a href="{{ route('role-permissions.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
