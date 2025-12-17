@extends('layouts.app')

@section('title','Reminder Tagihan')

@section('content_header')
<h1>Reminder Tagihan Jatuh Tempo</h1>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Status Reminder</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Kritis (>7 hari)</span>
                                <span class="info-box-number">{{ $summary['critical'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Peringatan (0-7 hari)</span>
                                <span class="info-box-number">{{ $summary['warning'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-bell"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Aktif</span>
                                <span class="info-box-number">{{ $summary['total_reminders'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Daftar Reminder</h5>
            </div>
            <div class="card-body">
                @if($summary['reminders']->isEmpty())
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Tidak ada reminder. Semua tagihan sudah lunas!
                    </div>
                @else
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Invoice</th>
                                <th>Penyewa</th>
                                <th>Kamar</th>
                                <th>Total Tagihan</th>
                                <th>Keterlambatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary['reminders'] as $idx => $reminder)
                            @php
                                $billing = $reminder->billing;
                                $statusClass = $reminder->days_overdue > 7 ? 'badge-danger' : 'badge-warning';
                            @endphp
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>{{ $billing->invoice_number }}</td>
                                <td>{{ $billing->consumer->nama ?? '-' }}</td>
                                <td>{{ $billing->room->nomor_kamar ?? '-' }}</td>
                                <td>Rp {{ number_format($billing->total_tagihan, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $reminder->days_overdue }} hari
                                    </span>
                                </td>
                                <td>
                                    @if($billing->status === 'lunas')
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif($billing->status === 'sebagian')
                                        <span class="badge badge-warning">Sebagian</span>
                                    @else
                                        <span class="badge badge-danger">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('billings.show', $billing) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
