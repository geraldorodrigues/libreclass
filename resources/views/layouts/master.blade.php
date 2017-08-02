<!DOCTYPE html>
<html>
	<head>
		<title>@yield('title')</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		{{ HTML::style('assets/css/bootstrap.min.css') }}
		{{ HTML::style('assets/lealjs/leal.min.css') }}
		{{ HTML::style('assets/css/master.min.css') }}
		{{ HTML::style('assets/css/summernote/summernote.min.css') }}

		{{--
		<link rel="stylesheet" href="/assets/css/master.min.css">
		<link rel="stylesheet" href="/lib/pikaday/pikaday.min.css">
		<link rel="stylesheet" href="/lib/tablesorter/tablesorter.min.css"> --}}

		@yield('styles')

		{{ HTML::script('js/jquery.min.js') }}
		{{ HTML::script('assets/lealjs/leal.min.js') }}
		{{ HTML::script('assets/js/bootstrap.min.js') }}
		{{ HTML::script('assets/js/summernote.min.js') }}
		{{ HTML::script('assets/js/scripts.min.js') }}

		{{-- <script src="/lib/moment.min.js"></script> --}}
		{{-- <script src="/lib/pikaday/pikaday.min.js"></script> --}}
		{{-- <script src="/lib/tablesorter/jquery.tablesorter.min.js"></script> --}}
		{{-- <script src="/lib/js/jquery.mask.min.js"></script> --}}

		{{-- <script src="/js/scripts.min.js"></script> --}}

		{{-- <script src="{{ elixir('/lib/leal/leal.min.js') }}"></script> --}}
		@yield('scripts')
	</head>

	<body>

		<div class="main">
			@yield('modals')

			<div class="page flex grow flex-column">

				<div class="page__side side">

					<div class="side__header color-white text-center">
						<div class="text-medium text-xmd" data-name="provider-name">
							{{-- @if(auth()->user()->type == "A")
								Administrador
							@endif --}}
						</div>
						<div>
							{{-- @if (auth()->user()->type == 'R')
								Olá, {{ auth()->user()->name }}
							@endif --}}
						</div>
					</div>
					<div class="side__body">
						@yield('side-menu')
					</div>
					<div class="side__footer">
					</div>
				</div>

				<div class="page__content page--scroll bg-color-grey--light">

					<div class="page__header">
						<div class="flex center-left">
							<div class="flex grow center-left">
								<div id="page-title" class="page__header-title">Title</div>
							</div>

							<div class="mr" title="Configurações">
								<i class="material-icons icon color-primary ck color-grey">&#xE8B8;</i>
							</div>{{--fim do icon de opções--}}
							<div title="Sair do sistema">
								<a href="logout"><i class="material-icons icon color-primary logout ck color-grey">&#xE879;</i></a>
							</div>
						</div>
					</div>

					<div class="container mt">
						@yield('content')
					</div>
				</div>
			</div>
		</div>{{--fim da div main--}}
	</body>
</html>
