<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>

		<link rel="icon" type="image/png" href="/images/favicon.png" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no ">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		{{ HTML::style('https://fonts.googleapis.com/icon?family=Material+Icons">')}}

		{{ HTML::style('assets/css/bootstrap.min.css') }}
		{{ HTML::style('assets/lealjs/leal.min.css') }}
		{{ HTML::style('css/home.css') }}


		{{ HTML::script('js/jquery.min.js') }}
		{{ HTML::script('assets/lealjs/leal.min.js') }}
		{{ HTML::script('assets/js/bootstrap.min.js') }}
		{{ HTML::script('/assets/js/loginController.min.js') }}

	</head>

	<body>
		<div class="container container-login" id="view-login">

			<img src="images/logo.svg" class="logomarca center-block" />
			<br>
			<h4 class="text-center">Faça login para acessar o<br><b>Libreclass Community Edition</b></h4>
			<br>
			{{-- @if (Session::has("info"))
				<div class="alert alert-info text-center alert-dismissible" role="alert" >
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<span class="text-sm">{{Session::get("info")}}</span>
				</div>
			@endif
			@if (Session::has("error"))
				<div class="alert alert-danger text-center alert-dismissible" role="alert" >
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<span class="text-sm">{{Session::get("error")}}</span>
				</div>
			@endif --}}
			<div class="panel panel-login">
				<div class="panel-body">

					<div class="row">
						<form id="form-login">
							<div class="col-md-12">
									<div class="form-group">
										<input name="email" type="text" placeholder="Digite seu email" class="form-control" regex="." autofocus />
										{{-- <div class="callback">Informe um email válido</div> --}}
									</div>
									<div class="form-group">
										<input type="password" name="password" placeholder="Digite a senha" class="form-control" regex=".">
										{{-- <div class="callback">Informe a senha</div> --}}
									</div>
							</div>
							<div class="col-md-12 col-xs-12">
								<button class="btn btn-shadow btn-primary btn-block">Entrar</button>
							</div>
							<div class="col-md-12 col-xs-12 text-center text-xs form-help">
								<ul class="list-inline">
									<li class="checkbox">
										<input type="checkbox">Continuar conectado
									</li>
									<li><a class="click" id="forgot-password">Esqueci minha senha</a></li>
								</ul>
							</div>
						</form>
					</div>

					<br>
					<div class="row">
						<div class="col-md-12 text-center">
							<a id="new-account" class="click">Criar nova conta</a>
						</div>
					</div>
				</div>
			</div>

		</div>
		@include('user.modalForgot')
		@include('user.modalNewAccount')


	</body>
</html>
