<?php
if (!defined('blumiga')) exit;

routeGET('/', 'home@index', 'home');
routeGET('/exemplo/banco', 'exemplo@banco', 'exemplo.banco');
routeGET('/exemplo/sessao', 'exemplo@sessao', 'exemplo.sessao');
routeGET('/exemplo/helpers', 'exemplo@helpers', 'exemplo.helpers');

routeGET('/login', 'auth@login', 'login');
routePOST('/login', 'auth@logar', 'login.logar');
routeGET('/logout', 'auth@logout', 'logout');

routeGROUP('/admin', 'Admin', function () {
    routeGET('/', 'dashboard@index', 'admin.home');
}, ['auth@run', 'log@run']);

route404(function () {
    view('errors/404');
});
