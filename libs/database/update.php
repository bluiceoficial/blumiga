<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class update extends database
{
    private array $sUpdate = [];

    public function add(string $name, mixed $value = '?'): self
    {
        $this->sUpdate[] = [
            'coluna' => "`" . trim($name, "`") . "`",
            'valor' => $value
        ];
        return $this;
    }

    public function update(): self
    {
        try {
            if (empty($this->sUpdate)) {
                return $this;
            }

            $valoresArr = [];
            foreach ($this->sUpdate as $row) {
                if (empty($this->sPreparado)) {
                    if (is_int($row['valor']) || is_float($row['valor'])) {
                        $valoresArr[] = "{$row['coluna']} = {$row['valor']}";
                    } elseif ($row['valor'] === null) {
                        $valoresArr[] = "{$row['coluna']} = NULL";
                    } else {
                        $escaped = mysqli_real_escape_string($this->sConecta, (string)$row['valor']);
                        $valoresArr[] = "{$row['coluna']} = '{$escaped}'";
                    }
                } else {
                    $valoresArr[] = "{$row['coluna']} = ?";
                }
            }

            $valores = implode(', ', $valoresArr);
            $sqlClausulas = $this->getWhere() . $this->getOrderBy() . $this->getLimit();

            $txt = sprintf('UPDATE %s SET %s%s', $this->getTable(), $valores, $sqlClausulas);

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
            $this->sUpdate = [];
            $this->sPreparado = [];
            $this->clearQueryState();
            return $this;
        }
    }
}
