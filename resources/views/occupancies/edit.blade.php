@extends('layouts.app')

@section('title', 'Edit Penyewaan')

@section('content_header')
    <h1>Edit Penyewaan</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @include('occupancies._form', ['occupancy' => $occupancy])
        </div>
    </div>
@endsection
