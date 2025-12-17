@extends('layouts.app')

@section('title','Daftar Pembayaran')

@section('content_header')
<h1>Daftar Pembayaran</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
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
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->billing->invoice_number ?? '-' }}</td>
                        <td>{{ $p->billing->consumer->nama ?? '-' }}</td>
                        <td>{{ optional($p->tanggal_bayar ? \Carbon\Carbon::parse($p->tanggal_bayar) : null)->format('Y-m-d') }}</td>
                        <td>Rp {{ number_format($p->jumlah,0,',','.') }}</td>
                        <td>{{ $p->metode ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection