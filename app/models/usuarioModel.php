<?php

namespace Blumiga\models\usuarioModel;

if (!defined('blumiga')) exit;

function listar(): array
{
    global $dbConfig;

    $conn = mysqli_connect(
        $dbConfig['default']['server'],
        $dbConfig['default']['username'],
        $dbConfig['default']['password'],
        $dbConfig['default']['database']
    );

    if (!$conn) {
        return [];
    }

    $result = mysqli_query($conn, "SELECT id, nome, email, criado_em FROM usuarios ORDER BY criado_em DESC");
    $dados = [];

    if ($result) {
        $dados = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
    }

    mysqli_close($conn);
    return $dados;
}

function buscarPorId(int $id): array
{
    global $dbConfig;

    $conn = mysqli_connect(
        $dbConfig['default']['server'],
        $dbConfig['default']['username'],
        $dbConfig['default']['password'],
        $dbConfig['default']['database']
    );

    if (!$conn) {
        return [];
    }

    $stmt = mysqli_prepare($conn, "SELECT id, nome, email, criado_em FROM usuarios WHERE id = ?");

    if (!$stmt) {
        mysqli_close($conn);
        return [];
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_assoc($result) ?: [];

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $dados;
}
