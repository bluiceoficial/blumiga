<?php

return [
    'run' => function () {
        global $dbConfig;

        $conn = mysqli_connect(
            $dbConfig['default']['server'],
            $dbConfig['default']['username'],
            $dbConfig['default']['password'],
            $dbConfig['default']['database']
        );

        if (!$conn) {
            echo "\033[31mErro de conexão: " . mysqli_connect_error() . "\033[0m\n";
            return;
        }

        $usuarios = [
            ['nome' => 'Alice Silva',     'email' => 'alice@localhost'],
            ['nome' => 'Bruno Oliveira',  'email' => 'bruno@localhost'],
            ['nome' => 'Carla Santos',    'email' => 'carla@localhost'],
            ['nome' => 'Diego Pereira',   'email' => 'diego@localhost'],
            ['nome' => 'Elena Costa',     'email' => 'elena@localhost'],
        ];

        $stmt = mysqli_prepare($conn, "INSERT INTO usuarios (nome, email) VALUES (?, ?)");

        if (!$stmt) {
            echo "\033[31mErro no prepare: " . mysqli_error($conn) . "\033[0m\n";
            mysqli_close($conn);
            return;
        }

        foreach ($usuarios as $u) {
            mysqli_stmt_bind_param($stmt, 'ss', $u['nome'], $u['email']);
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        echo "\033[32m" . count($usuarios) . " usuários inseridos com sucesso!\033[0m\n";
    },

    'down' => function () {
        global $dbConfig;

        $conn = mysqli_connect(
            $dbConfig['default']['server'],
            $dbConfig['default']['username'],
            $dbConfig['default']['password'],
            $dbConfig['default']['database']
        );

        if (!$conn) {
            echo "\033[31mErro de conexão: " . mysqli_connect_error() . "\033[0m\n";
            return;
        }

        if (mysqli_query($conn, "DELETE FROM usuarios")) {
            echo "\033[33mTodos os usuários removidos.\033[0m\n";
        } else {
            echo "\033[31mErro: " . mysqli_error($conn) . "\033[0m\n";
        }

        mysqli_close($conn);
    },
];
