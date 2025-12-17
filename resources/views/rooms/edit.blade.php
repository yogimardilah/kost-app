@extends('layouts.app')

@section('title', 'Edit Kamar')

@section('content_header')
	<h1>Edit Kamar</h1>
@endsection

@section('content')
	<div class="card">
		<div class="card-body">
			@include('rooms._form', ['room' => $room, 'kosts' => $kosts])
		</div>
	</div>
@endsection