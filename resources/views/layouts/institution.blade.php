@extends('layouts.master')

@section('title', 'Libreclass - Instituição')
@section('scripts')
	 {{ HTML::script('assets/js/institutionControllers.min.js') }}
@endsection

@section('side-menu')
	@include('institution/side-menu')
@endsection

@section('content')
	@include('institution/courses')
	@include('institution/periods')
	@include('institution/disciplines')
	@include('institution/classes')
@endsection
