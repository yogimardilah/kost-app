@extends('layouts.app')

@section('title','Catat Pembayaran')

@section('content_header')
<h1>Catat Pembayaran</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="billing_id">Invoice Tagihan <span class="text-danger">*</span></label>
                <select name="billing_id" id="billing_id" class="form-control @error('billing_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Invoice --</option>
                    @foreach($billings as $b)
                        <option value="{{ $b->id }}" 
                            {{ old('billing_id') == $b->id || ($billing && $billing->id == $b->id) ? 'selected' : '' }}>
                            {{ $b->invoice_number }} - {{ $b->consumer->nama ?? '-' }} (Rp {{ number_format($b->total_tagihan,0,',','.') }})
                        </option>
                    @endforeach
                </select>
                @error('billing_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="tanggal_bayar">Tanggal Pembayaran <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                    value="{{ old('tanggal_bayar', now()->format('Y-m-d')) }}" required>
                @error('tanggal_bayar')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Pembayaran <span class="text-danger">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                    step="0.01" min="0" value="{{ old('jumlah') }}" required>
                @error('jumlah')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="metode">Metode Pembayaran <span class="text-danger">*</span></label>
                <select name="metode" id="metode" class="form-control @error('metode') is-invalid @enderror" required>
                    <option value="">-- Pilih Metode --</option>
                    <option value="tunai" {{ old('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="transfer" {{ old('metode') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="cek" {{ old('metode') == 'cek' ? 'selected' : '' }}>Cek</option>
                </select>
                @error('metode')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="bukti_bayar">Keterangan / Bukti Pembayaran</label>
                <textarea name="bukti_bayar" id="bukti_bayar" class="form-control" rows="3">{{ old('bukti_bayar') }}</textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
                <a href="{{ route('billings.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
