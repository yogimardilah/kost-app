@extends('layouts.app')

@section('title', 'Tambah Penyewaan')

@section('content_header')
    <h1>Tambah Penyewaan</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
                @include('occupancies._form', ['occupancy' => null, 'selectedRoomId' => $selectedRoomId ?? null])
        </div>
    </div>
@endsection
