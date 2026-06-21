<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

if (!defined('blumiga')) exit;

require_once(dirname(__FILE__, 2) . '/config/config.php');

if ($blumegaSandbox) {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

require_once(dirname(__FILE__, 2) . '/vendor/autoload.php');

$blumiga_routePath = parse_url(getenv('REQUEST_URI'), PHP_URL_PATH);

require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/route.php');

include_once(dirname(__FILE__, 2) . '/config/routes.php');

$blumiga_routeMethod = getenv('REQUEST_METHOD');

$route_found = false;

if (isset($blumiga_routes[$blumiga_routeMethod])) {
    foreach ($blumiga_routes[$blumiga_routeMethod] as $registered_path => $route_data) {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $registered_path);
        $pattern = '#^' . $pattern . '/?$#';

        if (preg_match($pattern, $blumiga_routePath, $matches)) {
            array_shift($matches);

            $handler_string = $route_data['handler']; // Ex: "controllers/contato/contatoController@pageContato"
            $sub_ns         = $route_data['sub_namespace'];

            if (strpos($handler_string, '@') === false) {
                die("Erro Blumiga: Formato de rota inválido. Use 'controllers/pasta/arquivoController@funcao'.");
            }

            list($controller_path, $function_name) = explode('@', $handler_string);

            // Como Blumiga substitui a pasta 'app', nós apontamos fisicamente para dentro de '/app/'
            $sub_folder = !empty($sub_ns) ? str_replace('\\', '/', $sub_ns) : '';
            $file_path = dirname(__FILE__, 2) . '/app/controllers/' . $sub_folder . $controller_path . '.php';

            if (file_exists($file_path)) {
                include_once $file_path;
            } else {
                die("Erro Blumiga: O arquivo de controller '{$file_path}' não foi encontrado.");
            }

            // Substitui as barras do caminho e monta: \Blumiga\{sub_ns}{caminho_do_controller}\{funcao}
            $formatted_ns = str_replace('/', '\\', $controller_path);
            $full_function = '\\Blumiga\\' . $sub_ns . $formatted_ns . '\\' . $function_name;

            if (function_exists($full_function)) {
                $full_function(...$matches);
                $route_found = true;
            } else {
                die("Erro Blumiga: A função '{$function_name}' não existe dentro do namespace '{$full_function}'.");
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
