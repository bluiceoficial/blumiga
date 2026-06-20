<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class select extends database
{
    private array $sColunas = [];
    private array $sRows = [];

    public function innerJoin(string $name, ?string $on = null): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $joinStr = ' INNER JOIN ' . $prefixo . $name;

        if ($on !== null) {
            $joinStr .= ' ON ' . $on;
        }

        $this->sTabelas[] = $joinStr;
        $this->sDisablePrefix = false;
        return $this;
    }

    public function leftJoin(string $name, ?string $on = null): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $joinStr = ' LEFT JOIN ' . $prefixo . $name;

        if ($on !== null) {
            $joinStr .= ' ON ' . $on;
        }

        $this->sTabelas[] = $joinStr;
        $this->sDisablePrefix = false;
        return $this;
    }

    public function column(string $name, string $apelido = ''): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $finalName = (str_contains($name, '(')) ? $name : $prefixo . $name;

        $this->sColunas[] = empty($apelido) ? $finalName : sprintf('%s AS `%s`', $finalName, trim($apelido, '`'));
        $this->sDisablePrefix = false;
        return $this;
    }

    private function getColunas(): string
    {
        return !empty($this->sColunas) ? implode(', ', $this->sColunas) : '*';
    }

    public function select(): self
    {
        try {
            $txt = 'SELECT ';
            $txt .= $this->getColunas() . ' FROM ' . $this->getTable();
            $txt .= $this->getWhere();
            $txt .= $this->getGroupBy();
            $txt .= $this->getHaving();
            $txt .= $this->getOrderBy();
            $txt .= $this->getLimit();

            if (empty($this->sPreparado)) {
                $this->sResult = mysqli_query($this->sConecta, $txt);
                $this->sFechaResult = ($this->sResult instanceof \mysqli_result);
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
                    $this->sQuery = mysqli_stmt_get_result($stmt);
                }
            }
        } catch (\mysqli_sql_exception | \Exception $ex) {
            $this->log($ex);
            $this->sFechaResult = false;
        } finally {
            $this->clearQueryState();
            $this->sColunas = [];
        }

        return $this;
    }

    public function execute(): void
    {
        if ($this->sResult instanceof \mysqli_stmt) {
            mysqli_stmt_execute($this->sResult);
            $this->sQuery = mysqli_stmt_get_result($this->sResult);
        }
    }

    public function count(): int
    {
        if (empty($this->sPreparado)) {
            return $this->sResult instanceof \mysqli_result ? mysqli_num_rows($this->sResult) : 0;
        }
        return $this->sQuery instanceof \mysqli_result ? mysqli_num_rows($this->sQuery) : 0;
    }

    public function fetchArray(int $mode = MYSQLI_ASSOC): array|false|null
    {
        $target = empty($this->sPreparado) ? $this->sResult : $this->sQuery;
        return ($target instanceof \mysqli_result) ? mysqli_fetch_array($target, $mode) : false;
    }

    public function fetchAssoc(): array|false|null
    {
        $target = empty($this->sPreparado) ? $this->sResult : $this->sQuery;
        return ($target instanceof \mysqli_result) ? mysqli_fetch_assoc($target) : false;
    }

    public function fetchAll(): array
    {
        $target = empty($this->sPreparado) ? $this->sResult : $this->sQuery;
        return ($target instanceof \mysqli_result) ? mysqli_fetch_all($target, MYSQLI_ASSOC) : [];
    }

    public function rows(array $rows): void
    {
        $this->sRows = $rows;
    }

    public function row(string $name): mixed
    {
        return $this->sRows[$name] ?? '';
    }
}
