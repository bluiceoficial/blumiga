# Blumiga

[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.4-777bb4.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

O **Blumiga** é um microframework MVC para PHP desenvolvido utilizando programação procedural com conceitos inspirados em programação funcional.

Seu objetivo é oferecer apenas o essencial para estruturar aplicações web, mantendo um núcleo pequeno, rápido e fácil de entender.

Diferentemente de frameworks que incluem autenticação, ORM, cache, filas e diversos outros recursos por padrão, o Blumiga fornece somente a base da aplicação. Todo o restante pode ser adicionado através de bibliotecas independentes, sejam elas oficiais do ecossistema Blumiga ou de terceiros.

A proposta é simples:

> **Fornecer um núcleo sólido para que cada desenvolvedor monte apenas o framework que realmente precisa.**

---

# Filosofia

O Blumiga segue alguns princípios fundamentais:

* MVC simples e objetivo
* Programação procedural organizada
* Conceitos inspirados em programação funcional
* Arquitetura modular
* Baixo consumo de memória
* Baixa quantidade de dependências
* Compatível com Composer
* Compatível com bibliotecas PSR
* Sem funcionalidades desnecessárias no núcleo

---

# O que faz parte do núcleo

O núcleo do Blumiga é composto apenas pelos recursos necessários para iniciar uma aplicação MVC.

* Sistema de rotas
* Dispatcher
* Controllers
* Models
* Views
* Helpers
* Configuração
* Middlewares
* Tratamento de erros
* Autoload via Composer

---

# O que não faz parte do núcleo

O Blumiga não possui estes recursos por padrão.

Eles podem ser adicionados posteriormente utilizando bibliotecas independentes.

* Autenticação
* Autorização
* Sessão
* Cache
* CSRF
* Rate Limiter
* ORM
* Query Builder
* Logger
* Mail
* Upload
* Eventos
* Filas
* OAuth
* API Resources
* WebSockets

Essa abordagem permite que cada projeto utilize apenas os componentes realmente necessários.

---

# Requisitos

* PHP 8.4 ou superior
* Composer
* Apache, Nginx ou qualquer servidor compatível com PHP

---

# Instalação

```bash
composer create-project bluiceoficial/blumiga meu-projeto
```

---

# Estrutura do projeto

```text
meu-projeto/
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│
├── config/
│   ├── config.php
│   └── routes.php
│
├── core/
│
├── public/
│   └── index.php
│
├── storage/
│
├── vendor/
│
└── composer.json
```

---

# Criando uma rota

```php
routeGET('/', 'HomeController@index');

routeGET('/contato', 'ContatoController@index');

routeGET('/produto/{id}', 'ProdutoController@show');

routePOST('/login', 'LoginController@store');
```

Também é possível criar grupos de rotas.

```php
routeGroup('/admin', 'admin', function () {

    routeGET('/dashboard', 'DashboardController@index');

    routeGET('/usuarios', 'UsuarioController@index');

});
```

Página 404 personalizada.

```php
route404(function () {

    view('errors/404');

});
```

---

# Controllers

```php
<?php

namespace App\Controllers;

function index(): void
{
    view('home', [
        'titulo' => 'Bem-vindo ao Blumiga'
    ]);
}
```

---

# Models

```php
<?php

namespace App\Models;

function listarUsuarios(): array
{
    return [];
}
```

---

# Views

```php
<!DOCTYPE html>

<html>

<head>

    <title><?= e($titulo) ?></title>

</head>

<body>

<h1><?= e($titulo) ?></h1>

</body>

</html>
```

---

# Helpers

Exemplo de escape para HTML.

```php
echo e($nome);
```

Renderização de views.

```php
view('home');
```

Renderização passando dados.

```php
view('produto', [

    'produto' => $produto

]);
```

---

# Bibliotecas

O Blumiga foi projetado para ser modular.

Você pode instalar apenas os componentes necessários.

Exemplo utilizando bibliotecas do ecossistema Blumiga.

```bash
composer require blumiga/auth
composer require blumiga/session
composer require blumiga/cache
composer require blumiga/validation
```

Ou utilizar bibliotecas de terceiros.

```bash
composer require symfony/security-core
composer require symfony/http-foundation
composer require monolog/monolog
```

Nada impede que o projeto utilize qualquer biblioteca compatível com Composer.

---

# Objetivos do projeto

* Ser simples de aprender
* Ser leve
* Ser rápido
* Ser previsível
* Ser modular
* Dar liberdade ao desenvolvedor
* Evitar dependências desnecessárias
* Facilitar manutenção de aplicações PHP

---

# Roadmap

* Sistema de rotas
* MVC
* Helpers
* Middlewares
* Sistema de Views
* Biblioteca de Autenticação
* Biblioteca de Sessões
* Biblioteca de Cache
* Biblioteca de Banco de Dados
* Biblioteca de Validação
* Biblioteca de Logs

---

# Contribuindo

Contribuições são bem-vindas.

Antes de enviar alterações, abra uma Issue para discutir mudanças significativas.

---

# Autor

**Murilo Gomes Julio**

🌐 https://www.bluice.com.br

📺 https://youtube.com/@bluiceoficial


---

## License

The Blumiga is provided under:

[SPDX-License-Identifier: MIT](https://github.com/bluiceoficial/blumiga/blob/main/LICENSE)

All contributions to the Blumiga are subject to this license.
