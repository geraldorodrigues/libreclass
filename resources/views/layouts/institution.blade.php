@extends('layouts.master')

@section('scripts')
	 {{ HTML::script('assets/js/institutionControllers.min.js') }}
@endsection

@section('content')
	@include('institution/courses');
@endsection
