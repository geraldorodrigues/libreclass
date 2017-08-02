@extends('layouts.master')

@section('scripts')
	 {{ HTML::script('assets/js/institutionControllers.min.js') }}
@endsection

@section('content')
	@include('institution/courses')
	@include('institution/periods')
	@include('institution/disciplines')
	@include('institution/classes')
@endsection