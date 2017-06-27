# LibreClass Community Edition

> Software LibreClass CE.

O LibreClass CE é mantido pela empresa Modeon Devhouse. É um projeto que surgiu a partir do LibreClass desenvolvido e mantido pela empresa Sysvale Softgroup [Github](https://github.com/Sysvale/libreclass).

## Instalação

Instale o apache:

    $ sudo apt-get install apache2

Instale o php5, mysql, composer, etc:

    $ sudo apt-get install apache2 php5 php5-mcrypt php5-curl php5-imagick php5-mysql
    $ sudo apt-get install mysql-server

Modifique o arquivo `apache2/php.ini`, na linha onde há `post_max_size` coloque o tamanho máximo de arquivo em 10M.

Habilite os módulos necessários:

    $ sudo a2enmod rewrite
    $ sudo php5enmod mcrypt
    $ sudo service apache2 restart

#### Configuração do arquivo `.env.php`

É necessário criar o arquivo `.env.php` de acordo com o arquivo `.env.php.example`, na raiz do projeto, com as informações para conexão ao banco de dados MySQL e configurações para envio de email. Este passo deve ser executado antes de instalar as dependências do projeto com o composer. Exemplo:

    <?php

    return [

        // General
        'KEY' => '7PeWDIaAGlf9OuS4yJfHjivZsFG5Nv19',
        'DEBUG' => true,

        // Database
        'DB_HOST' => '192.168.50.200',
        'DB_DATABASE' => 'libreclass-beta',
        'DB_USERNAME' => 'libreclass',
        'DB_PASSWORD' => 'libreClass1beta!',

        // Email
        'EMAIL_DRIVER' => 'smtp',
        'EMAIL_HOST' => 'mail.libreclass.com',
        'EMAIL_PORT' => 25,
        'EMAIL_FROMADD' => 'contato@libreclass.com',
        'EMAIL_FROMNAM' => 'LibreClass',
        'EMAIL_ENC' => 'tls',
        'EMAIL_UNAME' => 'contato@libreclass.com',
        'EMAIL_PASS' => 'SECRET',

    ];

Instale o composer:

    $ php -r "readfile('https://getcomposer.org/installer');" | php
    $ sudo cp composer.phar /bin/composer

Execute o composer na raiz do projeto para instalar as dependências necessárias:

    $ composer install

## Banco de dados
A estrutura do banco de dados poderá ser encontrada no projeto `doc` do LibreClass CE: [GitHub link](http://example.com/).

## Pós-instalação

#### Criando uma conta institucional

###### 1) Abrir o banco de dados pelo terminal:

    $ mysql -u root -p

###### 2) Selecionar o banco de dados do libreclass:

    mysql> use libreclass-beta

###### 3) Criar o usuário instituição (type = I) utilizando a string copiada no passo 3 como password:

    mysql> INSERT INTO `Users` (`email`, `password`, `name`, `type`, `gender`, `birthdate`, `uee`, `course`, `formation`, `cadastre`, `idCity`, `street`, `photo`, `enrollment`, `created_at`, `updated_at`) VALUES ('admin@email.com', '$2y$10$Azi/NDbx8WrjAsq0q9VMNeRKtUzoE4QRZOqXu/nQWsqocFXVKOQhu', 'Nome da Instituição', 'I', NULL, NULL, NULL, NULL, '0', 'N', NULL, NULL, '/images/user-photo-default.jpg', NULL, NULL, NULL);

Neste ponto você terá um usuário do tipo instituição. Poderá realizar login com o email `admin@email.com` e a senha `1234`.

## Contribuindo

Agradecemos caso deseje contribuir para o projeto!
