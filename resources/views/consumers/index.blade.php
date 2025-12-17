@extends('layouts.app')

@section('title', 'Daftar Konsumen')

@section('content_header')
    <h1>Daftar Konsumen</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('consumers.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Konsumen
            </a>
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

            @if($consumers->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Belum ada data konsumen. <a href="{{ route('consumers.create') }}">Buat konsumen baru</a>
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th>Kendaraan</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($consumers as $no => $consumer)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $consumer->nik }}</td>
                                <td>{{ $consumer->nama }}</td>
                                <td>{{ $consumer->no_hp }}</td>
                                <td>{{ $consumer->kendaraan ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('consumers.edit', $consumer->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('consumers.destroy', $consumer->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus konsumen ini?')">
                                            <i class="fas fa-trash"></i>
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
