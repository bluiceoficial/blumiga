<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class delete extends database
{
    public function delete(): self
    {
        try {
            $txt = 'DELETE FROM ' . $this->getTable();
            $txt .= $this->getWhere();
            $txt .= $this->getOrderBy();
            $txt .= $this->getLimit();

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
            $this->sPreparado = [];
            $this->clearQueryState();
            return $this;
        }
    }

    public function truncate(): self
    {
        try {
            $tableName = "`" . trim($this->getTable(), "`") . "`";

            $txt = sprintf('TRUNCATE TABLE %s', $tableName);

            mysqli_query($this->sConecta, $txt);

            $this->sFechaResult = false;
            $this->clearQueryState();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
        return $this;
    }
}
