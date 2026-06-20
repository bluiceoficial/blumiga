<?php
if (!defined('blumiga')) exit;

routeGET('/', 'homeController@index', 'home');

route404(function() {
    echo "Ops! Página 404.";
});
