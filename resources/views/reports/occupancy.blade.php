@extends('layouts.app')

@section('title','Laporan Hunian')

@section('content_header')
<h1>Laporan Hunian</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form action="{{ route('reports.occupancy') }}" method="GET" class="form-inline">
            <div class="row w-100">
                <div class="col-md-3 mb-2">
                    <input type="text" name="search" class="form-control form-control-sm w-100" placeholder="Cari kamar/penyewa/NIK..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="start_date" class="form-control form-control-sm w-100" placeholder="Tanggal Awal" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="end_date" class="form-control form-control-sm w-100" placeholder="Tanggal Akhir" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <select name="status" class="form-control form-control-sm w-100">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="terisi" {{ request('status') == 'terisi' ? 'selected' : '' }}>Terisi</option>
                        <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-search"></i> Filter</button>
                    <a href="{{ route('reports.occupancy') }}" class="btn btn-sm btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    <button type="button" class="btn btn-sm btn-info" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                    <a href="{{ route('reports.occupancy', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-sm btn-success"><i class="fas fa-file-excel"></i> Excel</a>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        @php
            $total = $occupancies->total();
            $active = \App\Models\RoomOccupancy::whereIn('status', ['terisi', 'aktif'])
                ->when(request('search'), function($q) {
                    $search = request('search');
                    $q->where(function($sub) use ($search) {
                        $sub->whereHas('room', fn($r) => $r->where('nomor_kamar', 'LIKE', "%{$search}%"))
                            ->orWhereHas('consumer', fn($c) => $c->where('nama', 'LIKE', "%{$search}%")->orWhere('nik', 'LIKE', "%{$search}%"));
                    });
                })
                ->when(request('start_date'), fn($q) => $q->whereDate('tanggal_masuk', '>=', request('start_date')))
                ->when(request('end_date'), fn($q) => $q->whereDate('tanggal_masuk', '<=', request('end_date')))
                ->count();
            $ended = $total - $active;
        @endphp

        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Occupancies</span>
                        <span class="info-box-number">{{ $total }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-bed"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active</span>
                        <span class="info-box-number">{{ $active }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ended</span>
                        <span class="info-box-number">{{ $ended }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Room</th>
                        <th>Penyewa</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($occupancies as $i => $o)
                        <tr>
                            <td>{{ $occupancies->firstItem() + $i }}</td>
                            <td>{{ $o->room->nomor_kamar ?? '-' }}</td>
                            <td>{{ $o->consumer->nama ?? '-' }}</td>
                            <td>
                                @if($o->tanggal_masuk)
                                    {{ \Carbon\Carbon::parse($o->tanggal_masuk)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($o->tanggal_keluar)
                                    {{ \Carbon\Carbon::parse($o->tanggal_keluar)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $o->status == 'aktif' || $o->status == 'terisi' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($o->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data occupancies.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Menampilkan {{ $occupancies->firstItem() ?? 0 }} - {{ $occupancies->lastItem() ?? 0 }} dari {{ $occupancies->total() }} data
            </div>
            <div>
                {{ $occupancies->links() }}
            </div>
        </div>
    </div>
</div>

<style media="print">
    .card-header, .btn, .pagination, .main-header, .main-sidebar, .main-footer {
        display: none !important;
    }
    .content-wrapper, .card {
        margin: 0 !important;
        padding: 0 !important;
    }
    table {
        font-size: 12px;
    }
    .info-box {
        page-break-inside: avoid;
    }
</style>

@endsection