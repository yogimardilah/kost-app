@extends('layouts.app')

@section('title', 'Tambah Konsumen')

@section('content_header')
    <h1>Tambah Konsumen</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @include('consumers._form', ['consumer' => null])
        </div>
    </div>
@endsection
