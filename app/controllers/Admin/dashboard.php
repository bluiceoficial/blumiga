<?php

namespace Blumiga\controllers\Admin\dashboard;

function index(): void
{
    $user = session('logado');

    view('admin/dashboard', [
        'titulo' => 'Admin — Dashboard',
        'user'   => $user,
    ], 'layout');
}
