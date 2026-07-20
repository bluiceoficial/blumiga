<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Site: https://www.bluice.com.br

if (!defined('blumiga')) exit;

if ($blumigaDev) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

require_once(dirname(__FILE__, 2) . '/vendor/autoload.php');

$blumiga_routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$blumiga_routeMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/route.php');

include_once(dirname(__FILE__, 2) . '/config/routes.php');

dispatchRoute($blumiga_routePath, $blumiga_routeMethod);