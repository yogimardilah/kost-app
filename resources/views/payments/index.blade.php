@extends('layouts.app')

@section('title','Daftar Pembayaran')

@section('content_header')
<h1>Daftar Pembayaran</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form class="form-inline mb-3" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="Cari invoice/penyewa/metode">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control mr-2">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control mr-2">
            <button class="btn btn-primary">Filter</button>
        </form>

        @if($payments->isEmpty())
            <div class="alert alert-info">Belum ada pembayaran.</div>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Penyewa</th>
                        <th>Tanggal</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $i => $p)
                    <tr>
                        <td>{{ $payments->firstItem() + $i }}</td>
                        <td>{{ $p->billing->invoice_number ?? '-' }}</td>
                        <td>{{ $p->billing->consumer->nama ?? '-' }}</td>
                        <td>{{ optional($p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar) : null)->format('Y-m-d') }}</td>
                        <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
                        <td>{{ $p->metode ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-between align-items-center">
                <div>Menampilkan {{ $payments->firstItem() }} - {{ $payments->lastItem() }} dari {{ $payments->total() }}</div>
                <div>{{ $payments->links() }}</div>
            </div>
        @endif
    </div>
</div>
@endsection