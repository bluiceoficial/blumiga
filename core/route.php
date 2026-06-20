<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

if (!defined('blumiga')) exit;

$blumiga_routes = [];
$blumiga_named_routes = [];
$blumiga_404_handler = null;
$blumiga_current_sub_namespace = '';

/**
 * Agrupa rotas sob um sub-namespace / sub-pasta física
 */
function routeGroup(string $sub_namespace, callable $callback): void {
    global $blumiga_current_sub_namespace;

    $previous_sub = $blumiga_current_sub_namespace;
    $blumiga_current_sub_namespace = trim($sub_namespace, '\\') . '\\';

    $callback();

    $blumiga_current_sub_namespace = $previous_sub;
}

function routeGET(string $path, string $handler, string $name = ''): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace;

    $blumiga_routes['GET'][$path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $path;
    }
}

function routePOST(string $path, string $handler, string $name = ''): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace;

    $blumiga_routes['POST'][$path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $path;
    }
}

function route404(mixed $function): void {
    global $blumiga_404_handler;
    $blumiga_404_handler = $function;
}

function route(string $name, array $params = []): string {
    global $blumiga_named_routes;

    if (!isset($blumiga_named_routes[$name])) {
        trigger_error("Rota com o nome '{$name}' não foi encontrada.", E_USER_WARNING);
        return '#';
    }

    $path = $blumiga_named_routes[$name];

    foreach ($params as $key => $value) {
        if (strpos($path, "{{$key}}") !== false) {
            $path = str_replace("{{$key}}", $value, $path);
            unset($params[$key]);
        }
    }

    if (!empty($params)) {
        $path .= '?' . http_build_query($params);
    }

    return $path;
}
