@extends('layouts.app')

@section('title', 'Edit Addon')

@section('content_header')
    <h1>Edit Addon Kamar</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @include('addons._form', ['addon' => $addon])
        </div>
    </div>
@endsection
