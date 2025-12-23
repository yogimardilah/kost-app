@extends('layouts.app')

@section('title','Transaksi Addon')

@section('content_header')
<h1>Transaksi Addon</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <form method="GET" class="form-inline">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Cari invoice/penyewa">
            <select name="status" class="form-control mr-2">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                <option value="sebagian" {{ request('status')=='sebagian' ? 'selected' : '' }}>Sebagian</option>
                <option value="lunas" {{ request('status')=='lunas' ? 'selected' : '' }}>Lunas</option>
            </select>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control mr-2" placeholder="Mulai">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control mr-2" placeholder="Selesai">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('addon-transactions.create') }}" class="btn btn-success ml-2">Tambah Addon ke Billing</a>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Penyewa</th>
                        <th>Kamar</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Sudah Bayar</th>
                        <th>Sisa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $i => $b)
                    <tr>
                        <td>{{ $billings->firstItem() + $i }}</td>
                        <td>{{ $b->invoice_number }}</td>
                        <td>{{ $b->created_at ? $b->created_at->format('d/m/Y') : '-' }}</td>
                        <td>{{ $b->consumer->nama ?? '-' }}</td>
                        <td>{{ $b->room->nomor_kamar ?? '-' }}</td>
                        <td>
                            @if($b->status=='pending')
                                <span class="badge badge-warning">Pending</span>
                            @elseif($b->status=='sebagian')
                                <span class="badge badge-info">Sebagian</span>
                            @elseif($b->status=='lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($b->status) }}</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($b->total_tagihan ?? 0,0,',','.') }}</td>
                        <td>Rp {{ number_format($b->total_paid ?? 0,0,',','.') }}</td>
                        <td>Rp {{ number_format($b->remaining ?? 0,0,',','.') }}</td>
                        <td>
                            <a href="{{ route('addon-transactions.create', ['consumer_id' => $b->consumer_id]) }}" class="btn btn-sm btn-success">Tambah Addon</a>
                            <a href="{{ route('payments.create', ['billing' => $b->id]) }}" class="btn btn-sm btn-primary">Bayar</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Belum ada tagihan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between">
            <div>Menampilkan {{ $billings->firstItem() }} - {{ $billings->lastItem() }} dari {{ $billings->total() }}</div>
            <div>{{ $billings->links() }}</div>
        </div>
    </div>
    
</div>
@endsection
