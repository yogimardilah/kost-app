@extends('layouts.app')

@section('title', 'Tambah Addon')

@section('content_header')
    <h1>Tambah Addon Kamar</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @include('addons._form', ['addon' => null])
        </div>
    </div>
@endsection
