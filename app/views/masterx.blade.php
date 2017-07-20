<!DOCTYPE html>
<html>

<head>
  <title>
    @section('title')
      LibreClass Social
    @show
  </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <!-- CSS are placed here -->
  {{ HTML::style('/vendor/bootstrap/css/bootstrap.min.css') }}
  {{ HTML::style('css/jquery-ui-1.9.2.custom.css') }}
  {{ HTML::style('/vendor/font-awesome/css/font-awesome.min.css') }}
  <!-- Scripts are placed here -->
  {{ HTML::script('/vendor/jquery/jquery.min.js') }}
  {{ HTML::script('/vendor/bootstrap/js/bootstrap.min.js') }}
</head>

<body>

  <div id="page">

    <div id=main_menu class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">

        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{{ URL::to('shop') }}}">Libreclass Beta</a>
        </div>

        <!-- Everything you want hidden at 940px or less, place within here -->
        <nav>
          <ul class="nav">
            <li><a href="{{{ URL::to('about') }}}">O que &eacute; o Libreclass</a></li>
            <li><a href="{{{ URL::to('terms') }}}">Termos de Uso</a></li>
            <li><a href="{{{ URL::to('privacy') }}}">Pol&iacute;tica de Privacidade</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            @section('logout')
            @show
          </ul>
        </nav>

      </div><!-- /.container -->
    </div><!-- /#main_menu -->

    <div id="header" align="center">
    </div><!-- /header -->

    @section('body')
    @show
    <div class="push" ></div>
    <div id="footer">
    </div><!-- /#footer -->
  </div><!-- /#page -->
</body>
</html>
