<?php

namespace Blumiga\controllers\home;

function index(): void
{
    view('home', [
        'titulo' => 'Blumiga — Documentação'
    ], 'layout');
}
