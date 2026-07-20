<?php
if (!defined('blumiga')) exit;

// Ambiente de desenvolvimento - exibe erros na página
$blumigaDev = true;

// Caso tenha o BlumigaDB instalado
$dbConfig['default'] = [
 'server' => '',
 'username' => '',
 'password' => '',
 'database' => ''
];

// Será preenchido automaticamente ou defina o nome da sessão
$sessionName = '';
