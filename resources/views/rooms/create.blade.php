@extends('layouts.app')

@section('title', 'Tambah Kamar')

@section('content_header')
	<h1>Tambah Kamar</h1>
@endsection

@section('content')
	<div class="card">
		<div class="card-body">
			@include('rooms._form', ['room' => null, 'kosts' => $kosts])
		</div>
	</div>
@endsection
