# LibreClass Community Edition

> Software LibreClass CE.

O LibreClass CE é mantido por empresas no Vale do São Francisco (Petrolina - PE e Juazeiro - BA). É um projeto que surgiu a partir do [LibreClass](https://github.com/Sysvale/libreclass).

## Instalação

#### 1. Em uma máquina linux Ubuntu 16.04 ou outra distribuição semelhante, instale os softwares necessários:

    $ sudo apt-get update && sudo apt-get install curl git apache2 php7.0 php7.0-mcrypt php7.0-curl php-imagick php7.0-mysql mysql-server

#### 2. Habilite os módulos necessários:

    $ sudo a2enmod rewrite && sudo phpenmod mcrypt && sudo service apache2 restart

#### 3. Instale o código do LibreClass CE:

    $ git clone https://github.com/LibreClass/libreclass.git

#### 4. Configure o aquivo `.env.php`

    $ cd libreclass
    $ cp .env.php.example .env.php

OBS: É necessário criar o arquivo `.env.php` de acordo com o arquivo `.env.php.example`, na raiz do projeto, com as informações para conexão ao banco de dados MySQL e configurações para envio de email. Este passo deve ser executado antes de instalar as dependências do projeto com o composer. Exemplo:

    <?php

    return [

        // General
        'KEY' => '7PeWDIaAGlf9OuS4yJfHjivZsFG5Nv19',
        'DEBUG' => true,

        // Database
        'DB_HOST' => '127.0.0.1',
        'DB_DATABASE' => 'libreclass',
        'DB_USERNAME' => 'root',
        'DB_PASSWORD' => '1234',

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

Atenção: Configure corretamente o `DB_USERNAME` e o `DB_PASSWORD` com o usuário e a senha do MySQL.

#### 5. Instale o composer:

    $ php -r "readfile('https://getcomposer.org/installer');" | php
    $ sudo cp composer.phar /bin/composer

#### 6. Execute o composer na raiz do projeto para instalar as dependências necessárias:

    $ composer install

## Banco de dados
A estrutura do banco de dados poderá ser encontrada no projeto `doc` do LibreClass CE: [GitHub link](https://github.com/LibreClass/doc).

## Pós-instalação

#### 7. Instalando o banco de dados

###### Execute no terminal o seguinte comando para instalar o banco de dados:

    $ DB="$(curl https://raw.githubusercontent.com/LibreClass/doc/master/database/db.sql)" && mysql -u root -p --execute="CREATE DATABASE libreclass; USE libreclass; $DB"

#### 8. Criando uma conta institucional

###### Criando um usuário instituição (type = I) com login `admin@email.com` e senha `1234`:

    $ mysql -u root -p --execute="USE libreclass; INSERT INTO Users (email, password, name, type, gender, birthdate, uee, course, formation, cadastre, idCity, street, photo, enrollment, created_at, updated_at) VALUES ('admin@email.com', '$2y$10$Azi/NDbx8WrjAsq0q9VMNeRKtUzoE4QRZOqXu/nQWsqocFXVKOQhu', 'Nome da Instituição', 'I', NULL, NULL, NULL, NULL, '0', 'N', NULL, NULL, '/images/user-photo-default.jpg', NULL, NULL, NULL);"

Neste ponto você terá um usuário do tipo instituição. Poderá realizar login com o email `admin@email.com` e a senha `1234`.

## Contribuindo

Agradecemos caso deseje contribuir para o projeto!

Faça parte da comunidade no [Facebook](https://www.facebook.com/groups/libreclassce) para ficar por dentro das novidades!

## Lista de empresas que mantém o LibreClass Community Edition

* [Modeon Devhouse](http://modeon.co)
* [Sysvale Softgroup](http://www.sysvale.com)
* Quer fazer parte desta lista? Demonstre o seu interesse através de issue. Gostaríamos de mostrar para a comunidade quais empresas contribuem para o crescimento deste projeto.
