<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class insert extends database
{
    private array $sInsert = [];

    public function add(string $name, mixed $value = '?'): self
    {
        $this->sInsert[] = [
            'coluna' => "`" . trim($name, "`") . "`",
            'valor' => $value
        ];
        return $this;
    }

    public function insert(): self
    {
        try {
            if (empty($this->sInsert)) {
                return $this;
            }

            $colunasArr = [];
            $valoresArr = [];

            foreach ($this->sInsert as $row) {
                $colunasArr[] = $row['coluna'];

                if (empty($this->sPreparado)) {
                    if (is_int($row['valor']) || is_float($row['valor'])) {
                        $valoresArr[] = $row['valor'];
                    } elseif ($row['valor'] === null) {
                        $valoresArr[] = 'NULL';
                    } else {
                        $escaped = mysqli_real_escape_string($this->sConecta, (string)$row['valor']);
                        $valoresArr[] = "'{$escaped}'";
                    }
                } else {
                    $valoresArr[] = '?';
                }
            }

            $colunas = implode(', ', $colunasArr);
            $valores = implode(', ', $valoresArr);

            $txt = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->getTable(), $colunas, $valores);

            if (empty($this->sPreparado)) {
                $this->sResult = mysqli_query($this->sConecta, $txt);
            } else {
                $sTipo = '';
                $sValores = [];
                foreach ($this->sPreparado as $row) {
                    $sTipo .= $row[0];
                    $sValores[] = $row[1];
                }

                if ($stmt = mysqli_prepare($this->sConecta, $txt)) {
                    mysqli_stmt_bind_param($stmt, $sTipo, ...$sValores);
                    mysqli_stmt_execute($stmt);
                    $this->sResult = $stmt;
                }
            }

            $this->sFechaResult = false;
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        } finally {
            $this->sInsert = [];
            $this->sPreparado = [];
            $this->clearQueryState();
            return $this;
        }
    }

    public function idinsert(): int|string
    {
        return $this->sConecta ? mysqli_insert_id($this->sConecta) : 0;
    }
}
