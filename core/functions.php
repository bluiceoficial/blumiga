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

// IP: Captura o IP do visitante
function getClientIP(): string {
    // Cloudflare
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    // Proxy confiável enviando o X-Forwarded-For
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

// Redirecionar
function redirect(string $url, mixed $params = '')
{
    $sParams = '?';

    if (is_array($params)) {
        foreach ($params as $name => $value) {
            $sParams .= sprintf('%s=%s&', $name, $value);
        }
    } else {
        $sParams = '';
    }

    $sParams = rtrim($sParams, '&');

    header('Location: ' . $url . $sParams);
    exit;
}

// JavaScript
function windowAlert(string $message)
{
    printf("<script>window.alert('%s');</script>", $message);
}

function redirectJS(string $url, mixed $params = '')
{
    $sParams = '?';

    if (is_array($params)) {
        foreach ($params as $name => $value) {
            $sParams .= sprintf('%s=%s&', $name, $value);
        }
    } else {
        $sParams = '';
    }

    $sParams = rtrim($sParams, '&');

    echo sprintf("<script>window.location.assign('%s%s');</script>", $url, $sParams);
    exit;
}

// Gerador de Senha
function generatePassword(int $length = 8, bool $uppercase = true, bool $numbers = true, bool $symbols = false): string
{
    $lmin = 'abcdefghijklmnopqrstuvwxyz';
    $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $num  = '1234567890';
    $simb = '!@#$%*-';

    $caracteres = $lmin;
    $caracteres .= ($uppercase) ? $lmai : '';
    $caracteres .= ($numbers) ? $num : '';
    $caracteres .= ($symbols) ? $simb : '';

    $len = strlen($caracteres);
    $retorno = '';

    for ($n = 1; $n <= $length; $n++) {
        $index = random_int(0, $len - 1);
        $retorno .= $caracteres[$index];
    }

    return $retorno;
}

// Date Converter: Altera o formato de uma string de data.
function changeDate(string $date, string $currentFormat = 'd/m/Y', string $newFormat = 'Y-m-d'): string
{
    $dateTime = \DateTime::createFromFormat($currentFormat, $date);
    return $dateTime ? $dateTime->format($newFormat) : '';
}

// Day of Week: Retorna o dia da semana
function dayOfWeek(string $date, string $locale = 'pt_BR'): string
{
    $timestamp = strtotime($date);
    if (!$timestamp) {
        return '';
    }

    $formatter = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::NONE,
        \IntlDateFormatter::NONE,
        date_default_timezone_get(),
        \IntlDateFormatter::GREGORIAN,
        'EEEE'
    );

    $translatedDay = $formatter->format($timestamp);

    return $translatedDay ? $translatedDay : '';
}

// Currency Format: Formata um valor numérico para o padrão de moeda
function formatCurrency(float|string $value, string $currency = 'BRL', string $locale = 'pt_BR'): string
{
    $floatValue = (float)$value;
    $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
    $formattedValue = $formatter->formatCurrency($floatValue, strtoupper($currency));

    return $formattedValue !== false ? $formattedValue : number_format($floatValue, 2, ',', '.');
}

// Month Name: Retorna o nome do mês por extenso
function monthName(int $month, string $locale = 'pt_BR'): string
{
    if ($month < 1 || $month > 12) {
        return '';
    }

    $timestamp = mktime(0, 0, 0, $month, 1);

    $formatter = new \IntlDateFormatter(
        $locale,
        \IntlDateFormatter::NONE,
        \IntlDateFormatter::NONE,
        date_default_timezone_get(),
        \IntlDateFormatter::GREGORIAN,
        'LLLL'
    );

    $translatedMonth = $formatter->format($timestamp);

    return $translatedMonth ? ucfirst($translatedMonth) : '';
}

// Pad Number: Garante que um número tenha pelo menos 2 dígitos (adiciona zero à esquerda).
function padNumber(int|string $value): string
{
    return str_pad((string)$value, 2, '0', STR_PAD_LEFT);
}

// Remove Accents: Remove acentuação gráfica de uma string.
function removeAccents(string $value): string
{
    $cleaned = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    return $cleaned !== false ? $cleaned : $value;
}

// Remove Special Characters: Remove caracteres especiais e símbolos de uma string.
function removeSpecialChars(string $value): string
{
    $search = ["$", "@", "%", "&", "*", "/", "+", "#"];
    return str_replace($search, "", $value);
}

// Slug Generator: Cria links amigáveis (slugs) a partir de um título/texto.
function generateSlug(string $value): string
{
    $text = removeAccents($value);
    $text = removeSpecialChars($text);
    $text = strtolower(trim($text));
    $text = (string)preg_replace('/\s+/', '-', $text);
    return $text;
}

// Debug Array: Exibe estruturas de dados formatadas com a tag HTML pre.
function pre(mixed $value): void
{
    printf('<pre>%s</pre>', htmlspecialchars(print_r($value, true)));
}

// containsAny: Verifica se uma string contém algum dos termos enviados (aceita array ou string)
function containsAny(string $haystack, array|string $needle): bool
{
    $needles = (array)$needle;

    foreach ($needles as $query) {
        if (str_contains($haystack, $query)) {
            return true;
        }
    }
    return false;
}

// Data Encryption: Criptografa strings usando AES-256-CBC
function encrypt(string $value, string $key): string
{
    $cipher = 'aes-256-cbc';
    $ivLength = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($value, $cipher, $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}

// Data Decryption: Descriptografa strings usando AES-256-CBC.
function decrypt(string $value, string $key): string
{
    $cipher = 'aes-256-cbc';
    $data = base64_decode($value);
    $ivLength = openssl_cipher_iv_length($cipher);

    if (strlen($data) < $ivLength) {
        return '';
    }

    $iv = substr($data, 0, $ivLength);
    $encryptedData = substr($data, $ivLength);
    $decrypted = openssl_decrypt($encryptedData, $cipher, $key, OPENSSL_RAW_DATA, $iv);

    return $decrypted !== false ? $decrypted : '';
}

// Client Language: Detecta o idioma do navegador do visitante.
function clientLanguage(): string
{
    $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en-US';
    return substr($lang, 0, 5);
}

// Code Prefix Generator: Gera prefixos randômicos seguros de 5 caracteres.
function generatePrefix(): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyz';
    $maxIndex = strlen($chars) - 1;
    $prefix = '';

    for ($n = 1; $n <= 5; $n++) {
        // Trocado mt_rand por random_int (CSPRNG Seguro)
        $prefix .= $chars[random_int(0, $maxIndex)];
    }

    return $prefix;
}

// Create Directory: Cria diretórios de forma recursiva se não existirem.
function createDir(string $path): bool
{
    return is_dir($path) ? true : mkdir($path, 0755, true);
}

// Read File: Lê o conteúdo de um arquivo com segurança.
function readFileContent(string $filename): string
{
    if (!file_exists($filename) || is_dir($filename)) {
        return '';
    }
    $content = file_get_contents($filename);
    return $content !== false ? $content : '';
}

// Create/Write File: Cria ou anexa dados em um arquivo de texto.
function writeFileContent(string $filename, string $data, bool $replace = false): bool
{
    $flags = $replace ? 0 : FILE_APPEND;
    return file_put_contents($filename, $data, $flags) !== false;
}

// Delete File: Exclui um arquivo do disco se ele existir.
function deleteFile(string $filename): bool
{
    return file_exists($filename) && !is_dir($filename) ? @unlink($filename) : false;
}

// Delete Directory Recursive: Remove pastas e subpastas de forma recursiva com segurança.
function deleteDir(string $directory): bool
{
    if (!is_dir($directory)) {
        return false;
    }

    $items = scandir($directory);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;

        if (is_dir($path)) {
            deleteDir($path);
        } else {
            @unlink($path);
        }
    }

    return rmdir($directory);
}
