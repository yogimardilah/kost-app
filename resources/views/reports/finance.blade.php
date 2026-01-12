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
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari transaksi...">
            </div>
            <div class="form-group mr-2 mb-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" placeholder="Tanggal Invoice (Dari)">
            </div>
            <div class="form-group mr-2 mb-2">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" placeholder="Tanggal Invoice (Sampai)">
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
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Tagihan</span>
                        <span class="info-box-number">Rp {{ number_format($totalBilled ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pendapatan</span>
                        <span class="info-box-number">Rp {{ number_format($totalPaid ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Operasional</span>
                        <span class="info-box-number">Rp {{ number_format($totalExpenses ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-money-check-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Payroll</span>
                        <span class="info-box-number">Rp {{ number_format($totalPayroll ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-minus-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Pengeluaran (Ops + Payroll)</span>
                        <span class="info-box-number">Rp {{ number_format(($totalExpenses ?? 0) + ($totalPayroll ?? 0),0,',','.') }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-box">
                    <span class="info-box-icon {{ ($netIncome ?? 0) >= 0 ? 'bg-success' : 'bg-danger' }}"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Laba Bersih</span>
                        <span class="info-box-number">Rp {{ number_format($netIncome ?? 0,0,',','.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <strong><i class="fas fa-exclamation-triangle"></i> Tagihan Belum Dibayar:</strong> 
                    Rp {{ number_format($outstanding ?? 0,0,',','.') }}
                    <span class="ml-2 text-sm">(Piutang yang perlu ditagih)</span>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Referensi</th>
                        <th>Keterangan</th>
                        <th>Pendapatan</th>
                        <th>Pengeluaran</th>
                        <th>Status</th>
                        <th class="no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $i => $t)
                        <tr>
                            <td>{{ $transactions->firstItem() + $i }}</td>
                            <td>{{ \Carbon\Carbon::parse($t->transaction_date)->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if($t->type === 'billing')
                                    <span class="badge badge-primary">Tagihan</span>
                                @elseif($t->type === 'payroll')
                                    <span class="badge badge-info">Payroll</span>
                                @else
                                    <span class="badge badge-warning">Operasional</span>
                                @endif
                            </td>
                            <td>{{ $t->reference ?? '-' }}</td>
                            <td>
                                @if($t->type === 'billing')
                                    {{ $t->periode_awal && $t->periode_akhir ? \Carbon\Carbon::parse($t->periode_awal)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($t->periode_akhir)->format('d/m/Y') : '-' }}
                                @else
                                    {{ $t->description ?? '-' }}
                                @endif
                            </td>
                            <td class="text-success">
                                @if($t->type === 'billing')
                                    Rp {{ number_format($t->amount ?? 0,0,',','.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-danger">
                                @if($t->type === 'purchase' || $t->type === 'payroll')
                                    Rp {{ number_format($t->amount ?? 0,0,',','.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($t->type === 'billing')
                                    @if($t->status === 'lunas')
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif($t->status === 'sebagian')
                                        <span class="badge badge-warning">Sebagian</span>
                                    @else
                                        <span class="badge badge-danger">Pending</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">-</span>
                                @endif
                            </td>
                            <td class="no-print">
                                @if($t->type === 'billing')
                                    <a href="{{ route('billings.show', $t->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                    @if(auth()->user()->role_id === 1)
                                        <a href="{{ route('billings.edit', $t->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                @elseif($t->type === 'payroll')
                                    <a href="{{ route('payrolls.show', $t->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                    @if(auth()->user()->role_id === 1)
                                        <a href="{{ route('payrolls.edit', $t->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('purchases.edit', $t->id) }}" class="btn btn-sm btn-info">Lihat</a>
                                    @if(auth()->user()->role_id === 1)
                                        <a href="{{ route('purchases.edit', $t->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center no-print">
            <div>
                Menampilkan {{ $transactions->firstItem() }} - {{ $transactions->lastItem() }} dari {{ $transactions->total() }} data
            </div>
            <div>
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
