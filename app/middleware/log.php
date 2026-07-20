<?php

namespace Blumiga\middleware\log;

if (!defined('blumiga')) exit;

function run(callable $next, mixed $param = null): void
{
    $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $url = $_SERVER['REQUEST_URI'] ?? '/';
    $time = date('Y-m-d H:i:s');

    $log = "[{$time}] IP: {$ip} — URL: {$url} — Middleware: log" . ($param ? " (param: {$param})" : '') . PHP_EOL;

    $logDir = dirname(__DIR__, 2) . '/logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    file_put_contents($logDir . '/middleware.log', $log, FILE_APPEND | LOCK_EX);

    $next();
}
