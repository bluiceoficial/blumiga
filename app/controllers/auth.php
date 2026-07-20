<?php

namespace Blumiga\controllers\auth;

function login(): void
{
    view('auth/login', [
        'titulo' => 'Blumiga — Login',
    ], 'layout');
}

function logar(): void
{
    $usuario = inputPOST('usuario');
    $senha   = inputPOST('senha');

    $credenciais = [
        ['usuario' => 'admin', 'senha' => '123456', 'nome' => 'Administrador'],
        ['usuario' => 'user',  'senha' => '123456', 'nome' => 'Usuário Teste'],
    ];

    foreach ($credenciais as $c) {
        if ($c['usuario'] === $usuario && $c['senha'] === $senha) {
            $_SESSION['logado'] = [
                'usuario' => $c['usuario'],
                'nome'    => $c['nome'],
            ];
            redirect('/admin/');
        }
    }

    redirect('/login');
}

function logout(): void
{
    session_destroy();
    redirect('/login');
}
