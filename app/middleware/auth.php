<?php

namespace Blumiga\middleware\auth;

if (!defined('blumiga')) exit;

function run(callable $next, mixed $param = null): void
{
    if (empty($_SESSION['logado'])) {
        redirect('/login');
    }

    $next();
}
