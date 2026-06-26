<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

if (!defined('blumiga')) exit;

require_once(dirname(__FILE__, 2) . '/config/config.php');

if ($blumigaDev) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

require_once(dirname(__FILE__, 2) . '/vendor/autoload.php');

$blumiga_routePath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/route.php');

include_once(dirname(__FILE__, 2) . '/config/routes.php');

$blumiga_routeMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$route_found = false;

if (isset($blumiga_routes[$blumiga_routeMethod])) {
    foreach ($blumiga_routes[$blumiga_routeMethod] as $registered_path => $route_data) {
        // Se encontrar algo como {id:[0-9]+}, ele usa '[0-9]+'. Se encontrar apenas {id}, usa '[^/]+'
        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/', function ($matches) {
            // Se existir uma regex customizada após o ':', usa ela. Se não, usa o padrão para qualquer caractere
            return isset($matches[2]) ? '(' . $matches[2] . ')' : '([^/]+)';
        }, $registered_path);

        $pattern = '#^' . $pattern . '/?$#';

        if (preg_match($pattern, $blumiga_routePath, $matches)) {
            array_shift($matches);

            $handler_string = $route_data['handler']; // Ex: "controllers/contato/contatoController@pageContato"
            $sub_ns         = $route_data['sub_namespace'];

            if (strpos($handler_string, '@') === false) {
                error_log("Erro Blumiga: Formato de rota inválido. Use 'controllers/pasta/arquivoController@funcao'.");
            }

            list($controller_path, $function_name) = explode('@', $handler_string);

            // Como Blumiga substitui a pasta 'app', nós apontamos fisicamente para dentro de '/app/'
            $sub_folder = !empty($sub_ns) ? str_replace('\\', '/', $sub_ns) : '';
            $file_path = dirname(__FILE__, 2) . '/app/controllers/' . $sub_folder . $controller_path . '.php';

            if (file_exists($file_path)) {
                include_once $file_path;
            } else {
                error_log("Erro Blumiga: O arquivo de controller '{$file_path}' não foi encontrado.");
            }

            // Substitui as barras do caminho e monta: \Blumiga\{sub_ns}{caminho_do_controller}\{funcao}
            $formatted_ns = str_replace('/', '\\', $controller_path);
            $full_function = '\\Blumiga\\' . $sub_ns . $formatted_ns . '\\' . $function_name;

            if (function_exists($full_function)) {
                $full_function(...$matches);
                $route_found = true;
            } else {
                error_log("Erro Blumiga: A função '{$function_name}' não existe dentro do namespace '{$full_function}'.");
            }
            break;
        }
    }
}

if (!$route_found) {
    http_response_code(404);
    if (is_callable($blumiga_404_handler)) {
        ($blumiga_404_handler)();
    } else {
        echo "404 - Página não encontrada.";
    }
}
