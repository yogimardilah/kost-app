@extends('layouts.app')

@section('title', 'Daftar Pembelian/Ops')

@section('content_header')
    <h1>Daftar Pembelian/Ops</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12 mb-3">
                    <a href="{{ route('purchases.create') }}"
                       class="btn btn-primary btn-sm py-0 px-2">
                        <i class="fas fa-plus"></i> Tambah Pembelian
                    </a>
                </div>
                <div class="col-12">
                    <form method="GET" action="{{ route('purchases.index') }}">
                        <div class="form-row">
                            <div class="col-lg-2 col-md-2 col-sm-2 mb-2">
                                <input type="text" name="q" class="form-control form-control-sm"
                                       placeholder="Cari deskripsi" value="{{ request('q') }}">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-2 mb-2">
                                <select name="category" class="form-control form-control-sm">
                                    <option value="">-- Semua Kategori --</option>
                                    <option value="perawatan" {{ request('category') === 'perawatan' ? 'selected' : '' }}>Perawatan</option>
                                    <option value="perbaikan" {{ request('category') === 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                                    <option value="pembelian_barang" {{ request('category') === 'pembelian_barang' ? 'selected' : '' }}>Pembelian Barang</option>
                                    <option value="lainnya" {{ request('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <input type="date" name="date_from" class="form-control form-control-sm"
                                       value="{{ request('date_from') }}">
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-6 mb-2">
                                <input type="date" name="date_to" class="form-control form-control-sm"
                                       value="{{ request('date_to') }}">
                            </div>
                            <div class="col-auto mb-2">
                                <button class="btn btn-outline-secondary btn-sm" type="submit">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-auto mb-2">
                                <a href="{{ route('purchases.print', request()->all()) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-print"></i> Printout
                                </a>
                            </div>
                            <div class="col-auto mb-2">
                                <a href="{{ route('purchases.export', request()->all()) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-excel"></i> Export Excel
                                </a>
                            </div>
                            @if(request('q') || request('category') || request('date_from') || request('date_to'))
                                <div class="col-auto mb-2">
                                    <a href="{{ route('purchases.index') }}"
                                       class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
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

            @if($purchases->isEmpty())
                <div class="alert alert-info">Belum ada data pembelian.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kost</th>
                                <th>Deskripsi</th>
                                <th>Kategori</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $idx => $purchase)
                            <tr>
                                <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $idx + 1 }}</td>
                                <td>{{ $purchase->purchase_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $purchase->kost->nama_kost ?? '-' }}</span>
                                </td>
                                <td>{{ $purchase->description }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $purchase->category)) }}</span>
                                </td>
                                <td class="text-right">
                                    <strong>Rp {{ number_format($purchase->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> View/Edit
                                    </a>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $purchases->firstItem() ?? 0 }} - {{ $purchases->lastItem() ?? 0 }} dari {{ $purchases->total() }} data
                    </div>
                    <div>
                        {{ $purchases->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
