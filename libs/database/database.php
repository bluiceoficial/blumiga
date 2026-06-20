<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class database
{
    protected ?\mysqli $sConecta = null;
    protected \mysqli_result|\mysqli_stmt|bool|null $sResult = null;
    protected ?\mysqli_result $sQuery = null;
    protected string $sCharset = 'utf8mb4';

    protected array $sPreparado = [];
    protected bool $sFechaResult = false;

    protected string $sPrefix = '';
    protected bool $sDisablePrefix = false;
    protected array $sTabelas = [];
    protected array $sWhere = [];
    protected array $sOrderBy = [];
    protected array $sGroupBy = [];
    protected array $sHaving = [];
    protected string $sLimit = '';

    protected bool $sSemAspas = false;
    protected bool $sSemIgual = false;
    protected string $sAndOr = ' AND ';

    private bool $sSandbox = false;

    public function __construct(string $name = 'default')
    {
        global $dbConfig, $blumegaSandbox;

        try {
            $this->sSandbox = (bool)($blumegaSandbox ?? false);

            if (!isset($dbConfig[$name])) {
                throw new \Exception("Configuração de banco de dados '{$name}' não foi encontrada.");
            }

            $config = $dbConfig[$name];

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $this->sConecta = @mysqli_connect(
                $config['server'],
                $config['username'],
                $config['password'],
                $config['database'],
                $config['port'] ?? 3306
            );

            if (mysqli_connect_errno()) {
                throw new \Exception(mysqli_connect_error());
            }

            mysqli_set_charset($this->sConecta, $this->sCharset);

        } catch (\mysqli_sql_exception | \Exception $ex) {
            echo 'Não foi possível realizar a conexão com o banco de dados!';
            $this->log($ex->__toString());
        }
    }

    public function charset(string $value): self
    {
        $this->sCharset = $value;
        if ($this->sConecta instanceof \mysqli) {
            mysqli_set_charset($this->sConecta, $this->sCharset);
        }
        return $this;
    }

    public function commit(int $flags = 0, ?string $name = null): bool
    {
        return $this->sConecta ? mysqli_commit($this->sConecta, $flags, $name) : false;
    }

    public function rollback(int $flags = 0, ?string $name = null): bool
    {
        return $this->sConecta ? mysqli_rollback($this->sConecta, $flags, $name) : false;
    }

    public function multiQuery(string $sql): bool
    {
        return $this->sConecta ? mysqli_multi_query($this->sConecta, $sql) : false;
    }

    public function prefix(string $name): self
    {
        $this->sPrefix = $name;
        return $this;
    }

    public function disablePrefix(): self
    {
        $this->sDisablePrefix = true;
        return $this;
    }

    public function table(string $name): self
    {
        $this->sTabelas[] = ($this->sDisablePrefix || empty($this->sPrefix)) ? $name : $this->sPrefix . $name;
        $this->sDisablePrefix = false;
        return $this;
    }

    public function whereColumn(string $column1, string $column2): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $c1 = $prefixo . $column1;
        $c2 = $prefixo . $column2;

        $cláusula = "{$c1} = {$c2}";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;

        $this->resetWhereModifiers();
        return $this;
    }

    public function where(string $name, mixed $value = '?'): self
    {
        $txtAspas = "'";
        $txtIgual = '=';

        if ($this->sSemAspas || is_int($value) || is_float($value) || $value === '?') {
            $txtAspas = '';
        }

        if ($this->sSemIgual) {
            $txtIgual = '';
        }

        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $cláusula = $prefixo . $name . $txtIgual . $txtAspas . $value . $txtAspas;

        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;

        $this->resetWhereModifiers();
        return $this;
    }

    public function whereLike(string $column, string $value): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;

        if ($value === '?') {
            $cláusula = "{$prefixo}{$column} LIKE ?";
        } else {
            $escaped = mysqli_real_escape_string($this->sConecta, $value);
            $cláusula = "{$prefixo}{$column} LIKE '{$escaped}'";
        }

        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    public function whereContains(string $column, string $value): self
    {
        return $this->whereLike($column, "%{$value}%");
    }

    public function whereStartsWith(string $column, string $value): self
    {
        return $this->whereLike($column, "{$value}%");
    }

    public function whereEndsWith(string $column, string $value): self
    {
        return $this->whereLike($column, "%{$value}");
    }

    public function whereIn(string $column, array $values): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $escapedValues = array_map(function($val) {
            if (is_int($val) || is_float($val)) return $val;
            return "'" . mysqli_real_escape_string($this->sConecta, (string)$val) . "'";
        }, $values);

        $cláusula = "{$prefixo}{$column} IN (" . implode(', ', $escapedValues) . ")";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    public function whereNotIn(string $column, array $values): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $escapedValues = array_map(function($val) {
            if (is_int($val) || is_float($val)) return $val;
            return "'" . mysqli_real_escape_string($this->sConecta, (string)$val) . "'";
        }, $values);

        $cláusula = "{$prefixo}{$column} NOT IN (" . implode(', ', $escapedValues) . ")";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    public function whereBetween(string $column, mixed $start, mixed $end): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;

        $v1 = (is_int($start) || is_float($start)) ? $start : "'" . mysqli_real_escape_string($this->sConecta, $start) . "'";
        $v2 = (is_int($end) || is_float($end)) ? $end : "'" . mysqli_real_escape_string($this->sConecta, $end) . "'";

        $cláusula = "{$prefixo}{$column} BETWEEN {$v1} AND {$v2}";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    public function whereNull(string $column): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $cláusula = "{$prefixo}{$column} IS NULL";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $cláusula = "{$prefixo}{$column} IS NOT NULL";
        $this->sWhere[] = empty($this->sWhere) ? $cláusula : $this->sAndOr . $cláusula;
        $this->resetWhereModifiers();
        return $this;
    }

    private function resetWhereModifiers(): void
    {
        $this->sAndOr = ' AND ';
        $this->sSemAspas = false;
        $this->sSemIgual = false;
        $this->sDisablePrefix = false;
    }

    public function semAspas(): self
    {
        $this->sSemAspas = true;
        return $this;
    }

    public function semIgual(): self
    {
        $this->sSemIgual = true;
        return $this;
    }

    public function and(): self
    {
        $this->sAndOr = ' AND ';
        return $this;
    }

    public function or(): self
    {
        $this->sAndOr = ' OR ';
        return $this;
    }

    public function andNot(): self
    {
        $this->sAndOr = ' AND NOT ';
        return $this;
    }

    public function orNot(): self
    {
        $this->sAndOr = ' OR NOT ';
        return $this;
    }

    public function whereCustom(string $sql): self
    {
        if (!empty($this->sWhere) && !str_starts_with(trim($sql), 'AND') && !str_starts_with(trim($sql), 'OR')) {
            $this->sWhere[] = $this->sAndOr . $sql;
        } else {
            $this->sWhere[] = $sql;
        }
        $this->resetWhereModifiers();
        return $this;
    }

    public function orderby(string $name, bool $crescent = true): self
    {
        $direction = $crescent ? "ASC" : "DESC";
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $this->sOrderBy[] = "{$prefixo}{$name} {$direction}";
        $this->sDisablePrefix = false;
        return $this;
    }

    public function groupBy(string $column): self
    {
        $prefixo = ($this->sDisablePrefix || empty($this->sPrefix)) ? '' : $this->sPrefix;
        $this->sGroupBy[] = $prefixo . $column;
        $this->sDisablePrefix = false;
        return $this;
    }

    public function having(string $condition): self
    {
        $this->sHaving[] = $condition;
        return $this;
    }

    public function limit(int $page, int $registermax): self
    {
        $this->sLimit = sprintf('%d, %d', $page, $registermax);
        return $this;
    }

    public function prepared(mixed $value, string $type = 's'): self
    {
        $this->sPreparado[] = [$type, $value];
        return $this;
    }

    protected function getTable(): string
    {
        return !empty($this->sTabelas) ? implode('', $this->sTabelas) : '';
    }

    protected function getWhere(): string
    {
        return !empty($this->sWhere) ? ' WHERE ' . implode(' ', $this->sWhere) : '';
    }

    protected function getOrderBy(): string
    {
        return !empty($this->sOrderBy) ? ' ORDER BY ' . implode(', ', $this->sOrderBy) : '';
    }

    protected function getGroupBy(): string
    {
        return !empty($this->sGroupBy) ? ' GROUP BY ' . implode(', ', $this->sGroupBy) : '';
    }

    protected function getHaving(): string
    {
        return !empty($this->sHaving) ? ' HAVING ' . implode(' AND ', $this->sHaving) : '';
    }

    protected function getLimit(): string
    {
        return !empty($this->sLimit) ? ' LIMIT ' . $this->sLimit : '';
    }

    protected function clearQueryState(): void
    {
        $this->sTabelas = [];
        $this->sWhere = [];
        $this->sOrderBy = [];
        $this->sGroupBy = [];
        $this->sHaving = [];
        $this->sLimit = '';
    }

    protected function log(string|\Exception|\mysqli_sql_exception $message): void
    {
        $msg = ($message instanceof \Exception) ? $message->getMessage() : (string)$message;
        if ($this->sSandbox) {
            echo '<pre class="blumiga-error">' . htmlspecialchars($msg) . '</pre>';
        } else {
            error_log('[Blumiga DB Error]: ' . $msg);
        }
    }

    public function close(): void
    {
        if ($this->sResult instanceof \mysqli_stmt) {
            mysqli_stmt_close($this->sResult);
        } elseif ($this->sResult instanceof \mysqli_result && $this->sFechaResult) {
            mysqli_free_result($this->sResult);
        }

        if ($this->sQuery instanceof \mysqli_result) {
            mysqli_free_result($this->sQuery);
        }

        if ($this->sConecta instanceof \mysqli) {
            mysqli_close($this->sConecta);
        }

        $this->sConecta = null;
        $this->sResult = null;
        $this->sQuery = null;
    }
}
