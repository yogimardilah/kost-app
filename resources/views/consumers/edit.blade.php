@extends('layouts.app')

@section('title', 'Edit Konsumen')

@section('content_header')
    <h1>Edit Konsumen</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @include('consumers._form', ['consumer' => $consumer])
        </div>
    </div>
@endsection
