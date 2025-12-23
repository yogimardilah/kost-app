@extends('layouts.app')

@section('title', 'Daftar Addon')

@section('content_header')
    <h1>Daftar Addon Kamar</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('addons.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Addon
            </a>
            <form method="GET" action="{{ route('addons.index') }}" class="form-inline">
                <div class="input-group input-group-sm">
                    <input 
                        type="text" 
                        name="q" 
                        class="form-control" 
                        placeholder="Cari nama addon..."
                        value="{{ request('q') }}"
                    >
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(request('q'))
                            <a href="{{ route('addons.index') }}" class="btn btn-outline-danger ml-2">
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

            @if($addons->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    @if(request('q'))
                        Tidak ada addon yang sesuai dengan pencarian "{{ request('q') }}". <a href="{{ route('addons.index') }}">Hapus filter</a>
                    @else
                        Belum ada addon. <a href="{{ route('addons.create') }}">Buat addon baru</a>
                    @endif
                </div>
            @else
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Addon</th>
                            <th style="width: 150px;">Harga</th>
                            <th style="width: 100px;">Satuan</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addons as $no => $addon)
                            <tr>
                                <td>{{ ($addons->currentPage() - 1) * $addons->perPage() + $no + 1 }}</td>
                                <td><strong>{{ $addon->nama_addon }}</strong></td>
                                <td class="text-right">Rp {{ number_format($addon->harga, 0, ',', '.') }}</td>
                                <td>{{ $addon->satuan }}</td>
                                <td>
                                    <a href="{{ route('addons.edit', $addon->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('addons.destroy', $addon->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus addon ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada data addon</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $addons->firstItem() ?? 0 }} hingga {{ $addons->lastItem() ?? 0 }} dari {{ $addons->total() }} data
                    </div>
                    <div>
                        {{ $addons->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
