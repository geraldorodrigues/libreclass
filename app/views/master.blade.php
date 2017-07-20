<!doctype html>

<html lang="pt-br">

<head>

  <title>
    Libreclass CE
    @section('title')
    @show
  </title>

  <link rel="icon" type="image/png" href="/images/favicon.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  {{-- Vendor --}}
  {{ HTML::style('/vendor/bootstrap/css/bootstrap.min.css') }}
  {{ HTML::style('/vendor/font-awesome/css/font-awesome.min.css') }}

  {{ HTML::style('css/home.css') }}
  {{ HTML::style('css/validation.css') }}

  {{-- Vendor --}}
  {{ HTML::script('/vendor/jquery/jquery.min.js') }}
  {{ HTML::script('/vendor/bootstrap/js/bootstrap.min.js') }}

  {{ HTML::script('js/register.js') }}
  {{ HTML::script('js/menu.js') }}

  <script type="text/javascript">
    if (navigator.userAgent.match(/msie/i) || navigator.userAgent.match(/trident/i) ){
      window.location.href("/ie");
    }
  </script>

  @section('extraJS')
  @show

</head>

<body>

  @section('body')
  @show

  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-101695821-1', 'auto');
    ga('send', 'pageview');
  </script>
</body>
</html>
