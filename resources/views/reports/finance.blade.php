@extends('layouts.app')

@section('title','Laporan Keuangan')

@section('content_header')
<h1>Laporan Keuangan</h1>
@endsection

@section('content')
<style>
@media print {
    .no-print { display: none !important; }
    .table { font-size: 12px; }
}
</style>

<div class="card">
    <div class="card-header no-print">
        <form method="GET" class="form-inline">
            <div class="form-group mr-2 mb-2">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari invoice/penyewa/NIK/kamar">
            </div>
            <div class="form-group mr-2 mb-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
            </div>
            <div class="form-group mr-2 mb-2">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>
            <div class="form-group mr-2 mb-2">
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                    <option value="sebagian" {{ request('status')=='sebagian' ? 'selected' : '' }}>Sebagian</option>
                    <option value="lunas" {{ request('status')=='lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2 mr-2">Filter</button>
            <a href="{{ route('reports.finance') }}" class="btn btn-secondary mb-2 mr-2">Reset</a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success mb-2 mr-2">Export XLS</a>
            <button type="button" onclick="window.print()" class="btn btn-info mb-2">Print</button>
        </form>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tagihan</span>
                        <span class="info-box-number">Rp {{ number_format($totalBilled ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pembayaran</span>
                        <span class="info-box-number">Rp {{ number_format($totalPaid ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-exclamation-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Selisih Tagihan</span>
                        <span class="info-box-number">Rp {{ number_format($outstanding ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Penyewa</th>
                        <th>NIK</th>
                        <th>Kamar</th>
                        <th>Periode</th>
                        <th>Total Tagihan</th>
                        <th>Total Dibayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $i => $b)
                        @php
                            $paid = $b->payments->sum('jumlah');
                            $left = ($b->total_tagihan ?? 0) - $paid;
                        @endphp
                        <tr>
                            <td>{{ $billings->firstItem() + $i }}</td>
                            <td>{{ $b->invoice_number ?? '-' }}</td>
                            <td>{{ $b->consumer->nama ?? '-' }}</td>
                            <td>{{ $b->consumer->nik ?? '-' }}</td>
                            <td>{{ $b->room->nomor_kamar ?? '-' }}</td>
                            <td>
                                {{ $b->periode_awal ? \Carbon\Carbon::parse($b->periode_awal)->format('d/m/Y') : '-' }}
                                -
                                {{ $b->periode_akhir ? \Carbon\Carbon::parse($b->periode_akhir)->format('d/m/Y') : '-' }}
                            </td>
                            <td>Rp {{ number_format($b->total_tagihan ?? 0,0,',','.') }}</td>
                            <td>Rp {{ number_format($paid,0,',','.') }}</td>
                            <td>Rp {{ number_format($left,0,',','.') }}</td>
                            <td>
                                @if($b->status === 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($b->status === 'sebagian')
                                    <span class="badge badge-warning">Sebagian</span>
                                @else
                                    <span class="badge badge-danger">Pending</span>
                                @endif
                            </td>
                            <td class="no-print">
                                <a href="{{ route('billings.show', $b) }}" class="btn btn-sm btn-info">Lihat</a>
                                <a href="{{ route('billings.downloadInvoice', $b) }}" class="btn btn-sm btn-primary">Download</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Belum ada data tagihan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center no-print">
            <div>
                Menampilkan {{ $billings->firstItem() }} - {{ $billings->lastItem() }} dari {{ $billings->total() }} data
            </div>
            <div>
                {{ $billings->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
