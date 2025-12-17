@extends('layouts.app')

@section('title','Tagihan Detail')

@section('content_header')
<h1>Detail Tagihan</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('billings.index') }}" class="btn btn-secondary">&larr; Kembali</a>
            <a href="{{ route('billings.downloadInvoice', $billing) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download Invoice (PDF)
            </a>
        </div>

        <h4>Invoice: {{ $billing->invoice_number }}</h4>
        <p><strong>Penyewa:</strong> {{ $billing->consumer->nama ?? '-' }}</p>
        <p><strong>Kamar:</strong> {{ $billing->room->nomor_kamar ?? '-' }}</p>
        <p><strong>Periode:</strong> {{ $billing->periode_awal->format('Y-m-d') }} - {{ $billing->periode_akhir->format('Y-m-d') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($billing->status) }}</p>

        <h5 class="mt-4">Rincian Tagihan</h5>
        @if($billing->details->isEmpty())
            <div class="alert alert-info">Belum ada detail tagihan.</div>
        @else
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Deskripsi</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billing->details as $idx => $d)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $d->keterangan ?? '-' }}</td>
                        <td>{{ $d->qty }}</td>
                        <td>Rp {{ number_format($d->harga,0,',','.') }}</td>
                        <td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h5 class="mt-4">Pembayaran</h5>
        @if($billing->payments->isEmpty())
            <div class="alert alert-warning">Belum ada pembayaran.</div>
        @else
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billing->payments as $pidx => $p)
                    <tr>
                        <td>{{ $pidx + 1 }}</td>
                        <td>{{ optional($p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar) : null)->format('Y-m-d') }}</td>
                        <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
                        <td>{{ $p->metode ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="mt-4 p-3 bg-light border rounded">
            <div class="row">
                <div class="col-md-4">
                    <strong>Total Tagihan:</strong><br>
                    Rp {{ number_format($billing->total_tagihan,0,',','.') }}
                </div>
                <div class="col-md-4">
                    <strong>Total Dibayar:</strong><br>
                    Rp {{ number_format($billing->payments->sum('jumlah'),0,',','.') }}
                </div>
                <div class="col-md-4">
                    <strong>Sisa Tagihan:</strong><br>
                    Rp {{ number_format($billing->total_tagihan - $billing->payments->sum('jumlah'),0,',','.') }}
                </div>
            </div>
        </div>

        @if($billing->status !== 'lunas')
            <div class="mt-3">
                <a href="{{ route('payments.create', ['billing' => $billing->id]) }}" class="btn btn-success">
                    <i class="fas fa-credit-card"></i> Catat Pembayaran
                </a>
            </div>
        @else
            <div class="alert alert-success mt-3">
                <strong>Status:</strong> Tagihan telah lunas
            </div>
        @endif
    </div>
</div>
@endsection