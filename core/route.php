<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

if (!defined('blumiga')) exit;

$blumiga_routes = [];
$blumiga_named_routes = [];
$blumiga_404_handler = null;
$blumiga_current_sub_namespace = '';
$blumiga_current_url_prefix = '';

function routeGroup(string $url_prefix, string $sub_namespace, callable $callback): void {
    global $blumiga_current_sub_namespace, $blumiga_current_url_prefix;

    // Salva os estados anteriores para permitir grupos aninhados no futuro, se necessário
    $previous_sub = $blumiga_current_sub_namespace;
    $previous_prefix = $blumiga_current_url_prefix;

    // Trata e define o novo namespace e prefixo de URL
    $blumiga_current_sub_namespace = trim($sub_namespace, '\\') . '\\';

    // Garante que o prefixo comece com '/' e não termine com '/'
    $url_prefix = '/' . trim($url_prefix, '/');
    $blumiga_current_url_prefix = $previous_prefix . ($url_prefix === '/' ? '' : $url_prefix);

    $callback();

    // Restaura os estados anteriores após a execução do callback
    $blumiga_current_sub_namespace = $previous_sub;
    $blumiga_current_url_prefix = $previous_prefix;
}

function routeGET(string $path, string $handler, string $name = ''): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace, $blumiga_current_url_prefix;

    // Concatena o prefixo atual com o path da rota limpa
    $full_path = $blumiga_current_url_prefix . '/' . trim($path, '/');
    $full_path = $full_path === '/' ? '/' : rtrim($full_path, '/');

    $blumiga_routes['GET'][$full_path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $full_path;
    }
}

function routePOST(string $path, string $handler, string $name = ''): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace, $blumiga_current_url_prefix;

    // Concatena o prefixo atual com o path da rota limpa
    $full_path = $blumiga_current_url_prefix . '/' . trim($path, '/');
    $full_path = $full_path === '/' ? '/' : rtrim($full_path, '/');

    $blumiga_routes['POST'][$full_path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $full_path;
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
