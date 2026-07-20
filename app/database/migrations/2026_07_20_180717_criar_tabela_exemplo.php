<?php

return [
    'up' => function () {
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

        $sql = "CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(150) NOT NULL,
            email VARCHAR(200) NOT NULL,
            criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (mysqli_query($conn, $sql)) {
            echo "\033[32mTabela 'usuarios' criada com sucesso!\033[0m\n";
        } else {
            echo "\033[31mErro ao criar tabela: " . mysqli_error($conn) . "\033[0m\n";
        }

        mysqli_close($conn);
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

        if (mysqli_query($conn, "DROP TABLE IF EXISTS usuarios")) {
            echo "\033[33mTabela 'usuarios' removida com sucesso!\033[0m\n";
        } else {
            echo "\033[31mErro ao remover tabela: " . mysqli_error($conn) . "\033[0m\n";
        }

        mysqli_close($conn);
    },
];
