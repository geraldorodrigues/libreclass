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

    <p style="font-size: 16pt">{{ $data['name'] }} , seja bem vindo à nossa rede!</p>
  </center>

    <h4 style="font-size: 16pt">Recebemos a sua mensagem e responderemos o mais breve possível.</h4>

    <font style="font-size: 14pt; ">
      Informações:
      <ul>
          <li>Nome: {{ $data['name'] }}</li>
          <li>Telefone: {{ $data['phone'] }}</li>
          <li>Assunto: {{ $data['subject'] }}</li>
          <li>Mensagem: {{ $data['message'] }}</li>
      </ul>
    </font>

</body>
</html>