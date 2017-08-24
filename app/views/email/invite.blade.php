<html>

<head>
  <style>
    ul li {
      margin: 5px;
    }
  </style>
</head>

<body>
  <center>
    {{HTML::image(("https://app.libreclass.org/images/icon.png"), null, ["style" => "width: 200px;"])}}

    <p style="font-size: 16pt">Seja bem vindo ao Libreclass, {{ $name }}!</p>
  </center>
    <h4 style="font-size: 16pt"> Você foi convidado para fazer parte da rede {{ $institution }}.</h4>

    <font style="font-size: 14pt; ">Com o LibreClass você irá:

    <ul>
        <li>Aumentar a produtividade em sala de aula;</li>
        <li>Melhorar a comunicação entre professores, alunos e instituição;</li>
        <li>Reduzir a burocracia;</li>
        <li>Diminuir os gastos com papel;</li>
    </ul>
    </font>

    <h3>Dados de acesso:</h3>
    <font style="font-size: 12pt; ">

    <p><b>Página de Login: </b><a href="http://www.libreclass.org/login">www.libreclass.org/login</a></p>
    <p><b>Email: </b>{{ $email }}</p>
    <p><b>Senha: </b>{{ $password }}</p>
    </font>
</body>
</html>