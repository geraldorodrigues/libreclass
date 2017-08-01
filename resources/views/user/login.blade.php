@extends('master')

@section('title')
@parent
Login
@stop

@section('extraJS')
@parent
  {{ HTML::script('/js/additional-methods.min.js') }}
  {{ HTML::script('/js/jquery.validate.min.js') }}
  {{ HTML::script('/js/validations/usersLogin.js') }}
@stop

@section('body')
@parent

<div class="container container-login">

  {{ HTML::image("images/logo.svg", null ,["class" => "logomarca center-block"]) }}
  <br>
  <h4 class="text-center">Faça login para acessar o<br><b>Libreclass Community Edition</b></h4>
  <br>
  @if (Session::has("info"))
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
  @endif
  <div class="panel panel-login">
    <div class="panel-body">

      <div id="form-login">
        <div class="row">
          {{ Form::open(["url" => url("/auth/in"), "id" => "loginForm"]) }}
          <div class="col-md-12">
              <div class="form-group">
                {{ Form::text("email", null, ["placeholder" => "Digite seu email", "class" => "form-control", "required" => "required", "autofocus"]) }}
              </div>
              <div class="form-group">
                {{ Form::password("password", ["placeholder" => "Digite sua senha", "class" => "form-control", "required" => "required"]) }}
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
          {{ Form::close() }}
        </div>
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

@stop
