<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Site: https://www.bluice.com.br

// Models
function model(string $model_path): string
{
    $model_path = str_replace('\\', '/', $model_path);

    // Garante que o sufixo 'Model' exista no final do caminho informado
    if (substr($model_path, -5) !== 'Model') {
        $model_path .= 'Model';
    }

    // Pega apenas o nome do arquivo final (ex: 'homeModel')
    $parts = explode('/', $model_path);
    $model_name = end($parts);
    $base_models_dir = dirname(__FILE__, 2) . '/app/models/';

    // Cenário A: O arquivo está direto na raiz (ex: app/models/homeModel.php)
    $file_path = $base_models_dir . $model_path . '.php';

    // Cenário B: Se não existir na raiz, tenta na subpasta (ex: app/models/home/homeModel.php)
    if (!file_exists($file_path)) {
        $folder_name = str_replace('Model', '', $model_name);
        $file_path = $base_models_dir . $folder_name . '/' . $model_name . '.php';
    }

    if (file_exists($file_path)) {
        require_once($file_path);
    } else {
        die("Blumiga Erro: O arquivo do Model não foi encontrado na raiz e nem em subpastas: '{$file_path}'");
    }

    // Retorna o Namespace correto.
    return '\\Blumiga\\models\\' . $model_name . '\\';
}

// Views
function view(string $path, array $data = []): void
{
    $sPath = dirname(__FILE__, 2) . '/app/views/' . $path . '.php';

    if (file_exists($sPath)) {
        extract($data);
        include_once($sPath);
    } else {
        die("Blumiga Erro: A View '{$path}.php' não foi encontrada em: '{$sPath}'");
    }
}

// rotas
$blumiga_routeURLParts = array_values(array_filter(explode('/', $blumiga_routePath)));
$blumiga_routeURLs = [$blumiga_routePath, $blumiga_routeURLParts];

function getURL(int $number): string
{
    global $blumiga_routeURLs;
    return empty($blumiga_routeURLs[1][$number]) ? '' : $blumiga_routeURLs[1][$number];
}

function getFirstURL(): string
{
    global $blumiga_routeURLs;
    return empty($blumiga_routeURLs[1][0]) ? '' : $blumiga_routeURLs[1][0];
}

function getPenultimateURL(): string
{
    global $blumiga_routeURLs;
    return empty($blumiga_routeURLs[1][count($blumiga_routeURLs[1]) - 2]) ? '' : $blumiga_routeURLs[1][count($blumiga_routeURLs[1]) - 2];
}

function getLastURL(): string
{
    global $blumiga_routeURLs;
    return end($blumiga_routeURLs[1]);
}

// Anti XSS
function e(?string $value, int $flags = ENT_QUOTES|ENT_SUBSTITUTE, string $encoding = 'UTF-8'): ?string
{
    return (is_null($value)) ? '' : htmlspecialchars($value, $flags, $encoding);
}

// Forms
function inputGET(string $name, int $filter = FILTER_UNSAFE_RAW, array|int $options = 0): mixed
{
    return filter_input(INPUT_GET, $name, $filter, $options);
}

function emptyGET(string $name, int $filter = FILTER_UNSAFE_RAW, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_GET, $name, $filter, $options));
}

function inputPOST(string $name, int $filter = FILTER_UNSAFE_RAW, array|int $options = 0): mixed
{
    return filter_input(INPUT_POST, $name, $filter, $options);
}

function emptyPOST(string $name, int $filter = FILTER_UNSAFE_RAW, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_POST, $name, $filter, $options));
}

function requestPOST(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST';
}

function requestGET(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET';
}

// Diretório Raiz
function documentroot(): string
{
    return dirname(__FILE__, 2);
}

// Servername com ou sem protocolo e www
function servername(bool $comprotocolo = true, bool $semwww = false): string
{
    $servername = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';

    if ($semwww) {
        $servername = str_replace('www.', '', $servername);
    }

    if (!$comprotocolo) {
        return $servername;
    }

    $isHttps = false;

    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $isHttps = true;
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $isHttps = true;
    }

    $protocolo = $isHttps ? 'https://' : 'http://';

    return $protocolo . $servername;
}

/**
 * Request Path
 * Retorna a URI tratada e limpa para o sistema de rotas.
 */
function requestURI(): string
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';

    if ($path === '/') {
        return '/';
    }

    return rtrim($path, '/');
}
