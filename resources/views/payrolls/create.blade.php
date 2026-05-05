@extends('adminlte::page')

@section('title', 'Tambah Pembayaran')

@section('content_header')
    <h1>Tambah Pembayaran</h1>
@stop

@push('js')
    @include('partials.submit-loading-guard')
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Pembayaran</h3>
        </div>
        <form action="{{ route('payrolls.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('payrolls._form')
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
@stop
