<?php

namespace Blumiga\controllers\exemplo;

function banco(): void
{
    model('usuarioModel');
    $usuarios = \Blumiga\models\usuarioModel\listar();
    $total = count($usuarios);

    view('exemplo/banco', [
        'titulo'   => 'Exemplo — Banco de Dados',
        'usuarios' => $usuarios,
        'total'    => $total,
    ], 'layout');
}

function sessao(): void
{
    $contador = (session('contador') ?? 0) + 1;
    $_SESSION['contador'] = $contador;

    view('exemplo/sessao', [
        'titulo'    => 'Exemplo — Sessão',
        'contador'  => $contador,
    ], 'layout');
}

function helpers(): void
{
    view('exemplo/helpers', [
        'titulo' => 'Exemplo — Helpers',
    ], 'layout');
}
