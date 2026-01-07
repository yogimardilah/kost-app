@extends('adminlte::page')

@section('title', 'Edit Payroll')

@section('content_header')
    <h1>Edit Payroll</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Payroll</h3>
        </div>
        <form action="{{ route('payrolls.update', $payroll) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('payrolls._form')
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
@stop
