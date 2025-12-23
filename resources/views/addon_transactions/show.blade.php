@extends('layouts.app')

@section('title','Detail Transaksi Addon')

@section('content_header')
<h1>Detail Transaksi Addon</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div><strong>Invoice:</strong> {{ $trx->invoice_number }}</div>
                <div><strong>Tanggal:</strong> {{ $trx->tanggal ? \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') : '-' }}</div>
                <div><strong>Status:</strong> {{ ucfirst($trx->status) }}</div>
            </div>
            <div class="col-md-4">
                <div><strong>Penyewa:</strong> {{ $trx->consumer->nama ?? '-' }}</div>
                <div><strong>NIK:</strong> {{ $trx->consumer->nik ?? '-' }}</div>
            </div>
            <div class="col-md-4">
                <div><strong>Kamar:</strong> {{ $trx->room->nomor_kamar ?? '-' }}</div>
                <div><strong>Total:</strong> Rp {{ number_format($trx->total ?? 0,0,',','.') }}</div>
            </div>
        </div>
        <hr>
        <h5>Detail</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Addon</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trx->details as $i => $d)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $d->nama_addon }}</td>
                        <td>{{ $d->qty }}</td>
                        <td>Rp {{ number_format($d->harga,0,',','.') }}</td>
                        <td>Rp {{ number_format($d->subtotal,0,',','.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <a href="{{ route('addon-transactions.index') }}" class="btn btn-secondary">Kembali</a>
            @if($trx->status=='pending')
            <form action="{{ route('addon-transactions.post',$trx) }}" method="POST" class="d-inline" onsubmit="return confirm('Posting ke Billing?');"><br>                @csrf
                <button class="btn btn-primary">Post ke Billing</button>
            </form>
            @else
            <span class="badge badge-success">Posted</span>
            @endif
        </div>
    </div>
</div>
@endsection
