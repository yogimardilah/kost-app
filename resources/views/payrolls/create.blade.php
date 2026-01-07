@extends('adminlte::page')

@section('title', 'Tambah Payroll')

@section('content_header')
    <h1>Tambah Payroll</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Payroll</h3>
        </div>
        <form action="{{ route('payrolls.store') }}" method="POST">
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
