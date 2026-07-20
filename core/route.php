<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Site: https://www.bluice.com.br

$blumiga_routes = [];
$blumiga_named_routes = [];
$blumiga_404_handler = null;
$blumiga_current_sub_namespace = '';
$blumiga_current_url_prefix = '';
$blumiga_current_middleware = [];

function routeGroup(string $url_prefix, string $sub_namespace, callable $callback, array $middleware = []): void {
    global $blumiga_current_sub_namespace, $blumiga_current_url_prefix, $blumiga_current_middleware;

    $previous_sub = $blumiga_current_sub_namespace;
    $previous_prefix = $blumiga_current_url_prefix;
    $previous_middleware = $blumiga_current_middleware;

    $trimmed = trim($sub_namespace, '\\');
    $blumiga_current_sub_namespace = $trimmed !== '' ? $trimmed . '\\' : '';

    $url_prefix = '/' . trim($url_prefix, '/');
    $blumiga_current_url_prefix = $previous_prefix . ($url_prefix === '/' ? '' : $url_prefix);
    $blumiga_current_middleware = array_merge($previous_middleware, $middleware);

    $callback();

    $blumiga_current_sub_namespace = $previous_sub;
    $blumiga_current_url_prefix = $previous_prefix;
    $blumiga_current_middleware = $previous_middleware;
}

function routeGET(string $path, string|callable $handler, string $name = '', array $middleware = []): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace, $blumiga_current_url_prefix, $blumiga_current_middleware;

    $full_path = $blumiga_current_url_prefix . '/' . trim($path, '/');
    $full_path = $full_path === '/' ? '/' : rtrim($full_path, '/');

    $resolved = [];
    foreach (array_merge($blumiga_current_middleware, $middleware) as $mw) {
        if (str_contains($mw, '@')) {
            $parts = explode('@', $mw);
            $path_mw = $parts[0];
            $func = $parts[1] ?? 'run';
            $param_suffix = '';
            if (str_contains($func, ':')) {
                [$func, $param] = explode(':', $func, 2);
                $param_suffix = ':' . $param;
            }
            $filePath = dirname(__FILE__, 2) . '/app/middleware/' . $path_mw . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                error_log("Blumiga Erro: Middleware '{$filePath}' não encontrado.");
                continue;
            }
            $resolved[] = '\\Blumiga\\middleware\\' . str_replace('/', '\\', $path_mw) . '\\' . $func . $param_suffix;
        } else {
            $resolved[] = $mw;
        }
    }

    $blumiga_routes['GET'][$full_path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace,
        'middleware'    => $resolved,
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $full_path;
    }
}

function routePOST(string $path, string|callable $handler, string $name = '', array $middleware = []): void {
    global $blumiga_routes, $blumiga_named_routes, $blumiga_current_sub_namespace, $blumiga_current_url_prefix, $blumiga_current_middleware;

    $full_path = $blumiga_current_url_prefix . '/' . trim($path, '/');
    $full_path = $full_path === '/' ? '/' : rtrim($full_path, '/');

    $resolved = [];
    foreach (array_merge($blumiga_current_middleware, $middleware) as $mw) {
        if (str_contains($mw, '@')) {
            $parts = explode('@', $mw);
            $path_mw = $parts[0];
            $func = $parts[1] ?? 'run';
            $param_suffix = '';
            if (str_contains($func, ':')) {
                [$func, $param] = explode(':', $func, 2);
                $param_suffix = ':' . $param;
            }
            $filePath = dirname(__FILE__, 2) . '/app/middleware/' . $path_mw . '.php';
            if (file_exists($filePath)) {
                require_once $filePath;
            } else {
                error_log("Blumiga Erro: Middleware '{$filePath}' não encontrado.");
                continue;
            }
            $resolved[] = '\\Blumiga\\middleware\\' . str_replace('/', '\\', $path_mw) . '\\' . $func . $param_suffix;
        } else {
            $resolved[] = $mw;
        }
    }

    $blumiga_routes['POST'][$full_path] = [
        'handler'       => $handler,
        'sub_namespace' => $blumiga_current_sub_namespace,
        'middleware'    => $resolved,
    ];

    if ($name) {
        $blumiga_named_routes[$name] = $full_path;
    }
}

function route404(mixed $function): void {
    global $blumiga_404_handler;
    $blumiga_404_handler = $function;
}

function dispatchRoute(string $path, string $method): void
{
    global $blumiga_routes, $blumiga_404_handler;

    http_response_code(200);
    $blumiga_routeMethod = $method;
    $route_found = false;

    if (isset($blumiga_routes[$blumiga_routeMethod])) {
        foreach ($blumiga_routes[$blumiga_routeMethod] as $registered_path => $route_data) {
            $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)(?::([^}]+))?\}/', function ($m) {
                return isset($m[2]) ? '(' . $m[2] . ')' : '([^/]+)';
            }, $registered_path);

            $pattern = '#^' . $pattern . '/?$#';

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);

                $handler_entry = $route_data['handler'];
                $sub_ns        = $route_data['sub_namespace'];

                if (is_callable($handler_entry)) {
                    $middleware_list = $route_data['middleware'] ?? [];
                    $callable = $handler_entry;

                    if (!empty($middleware_list)) {
                        $next = function () use ($callable, $matches, &$route_found) {
                            $callable(...$matches);
                            $route_found = true;
                        };
                        $middleware_failed = false;
                        for ($i = count($middleware_list) - 1; $i >= 0; $i--) {
                            $name = $middleware_list[$i];
                            $param = null;
                            if (str_contains($name, ':')) {
                                [$name, $param] = explode(':', $name, 2);
                            }
                            if (!function_exists($name)) {
                                error_log("Erro Blumiga: Middleware '{$name}' não é uma função válida.");
                                $middleware_failed = true;
                                break;
                            }
                            $current = $next;
                            $next = function () use ($name, $current, $param) {
                                $name($current, $param);
                            };
                        }
                        if (!$middleware_failed) {
                            $next();
                        }
                    } else {
                        $callable(...$matches);
                        $route_found = true;
                    }
                    break;
                }

                if (strpos($handler_entry, '@') === false) {
                    error_log("Erro Blumiga: Formato de rota inválido. Use 'controllers/pasta/arquivoController@funcao'.");
                    break;
                }

                list($controller_path, $function_name) = explode('@', $handler_entry);

                $sub_folder = !empty($sub_ns) ? str_replace('\\', '/', $sub_ns) : '';
                $file_path = dirname(__FILE__, 2) . '/app/controllers/' . $sub_folder . $controller_path . '.php';

                if (file_exists($file_path)) {
                    require_once($file_path);
                } else {
                    error_log("Erro Blumiga: O arquivo de controller '{$file_path}' não foi encontrado.");
                }

                $formatted_ns = str_replace('/', '\\', $controller_path);
                $full_function = '\\Blumiga\\controllers\\' . $sub_ns . $formatted_ns . '\\' . $function_name;

                if (function_exists($full_function)) {
                    $middleware_list = $route_data['middleware'] ?? [];

                    if (!empty($middleware_list)) {
                        $next = function () use ($full_function, $matches, &$route_found) {
                            $full_function(...$matches);
                            $route_found = true;
                        };
                        $middleware_failed = false;
                        for ($i = count($middleware_list) - 1; $i >= 0; $i--) {
                            $name = $middleware_list[$i];
                            $param = null;
                            if (str_contains($name, ':')) {
                                [$name, $param] = explode(':', $name, 2);
                            }
                            if (!function_exists($name)) {
                                error_log("Erro Blumiga: Middleware '{$name}' não é uma função válida.");
                                $middleware_failed = true;
                                break;
                            }
                            $current = $next;
                            $next = function () use ($name, $current, $param) {
                                $name($current, $param);
                            };
                        }
                        if (!$middleware_failed) {
                            $next();
                        }
                    } else {
                        $full_function(...$matches);
                        $route_found = true;
                    }
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
            $errorView = dirname(__FILE__, 2) . '/app/views/errors/404.php';
            if (file_exists($errorView)) {
                require_once($errorView);
            } else {
                echo "404 - Página não encontrada.";
            }
        }
    }
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
