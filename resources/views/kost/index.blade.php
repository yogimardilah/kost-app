@extends('adminlte::page')

@section('title', 'Data Kost')

@section('content_header')
    <h1>Data Kost</h1>
@stop

@section('content')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <a href="{{ route('kost.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Tambah Kost
        </a>
        <form method="GET" action="{{ route('kost.index') }}" class="form-inline">
            <div class="input-group input-group-sm">
                <input 
                    type="text" 
                    name="q" 
                    class="form-control" 
                    placeholder="Cari nama, alamat, atau kota..."
                    value="{{ request('q') }}"
                >
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Cari
                    </button>
                    @if(request('q'))
                        <a href="{{ route('kost.index') }}" class="btn btn-outline-danger ml-2">
                            <i class="fas fa-times"></i> Reset
                        </a>
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

        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Kost</th>
                    <th>Alamat</th>
                    <th>Kota</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kosts as $no => $kost)
                    <tr>
                        <td>{{ ($kosts->currentPage() - 1) * $kosts->perPage() + $no + 1 }}</td>
                        <td><strong>{{ $kost->nama_kost }}</strong></td>
                        <td>{{ Str::limit($kost->alamat, 50) }}</td>
                        <td>{{ $kost->kota ?? '-' }}</td>
                        <td>
                            <a href="{{ route('kost.edit', $kost->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kost.destroy', $kost->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data kost</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Menampilkan {{ $kosts->firstItem() ?? 0 }} hingga {{ $kosts->lastItem() ?? 0 }} dari {{ $kosts->total() }} data
            </div>
            <div>
                {{ $kosts->links() }}
            </div>
        </div>
    </div>
</div>

@stop
