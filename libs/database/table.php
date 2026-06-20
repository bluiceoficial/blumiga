<?php
// Copyright (C) 2026 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only
// Site: https://www.bluice.com.br

namespace Blumiga\database;

class table extends database
{
    private const TYPE_VARCHAR    = 'VARCHAR';
    private const TYPE_CHAR       = 'CHAR';
    private const TYPE_INT        = 'INT';
    private const TYPE_TINYINT    = 'TINYINT';
    private const TYPE_BIGINT     = 'BIGINT';
    private const TYPE_TEXT       = 'TEXT';
    private const TYPE_MEDIUMTEXT = 'MEDIUMTEXT';
    private const TYPE_LONGTEXT   = 'LONGTEXT';
    private const TYPE_BLOB       = 'BLOB';
    private const TYPE_DATETIME   = 'DATETIME';
    private const TYPE_DECIMAL    = 'DECIMAL';
    private const TYPE_JSON       = 'JSON';

    private string $sEngine = 'MyISAM';
    private array $sCreateColumns = [];

    private string $currentType = self::TYPE_VARCHAR;
    private int $ctTamanho = 45;
    private int $ctPrecision = 10;
    private int $ctScale = 2;
    private bool $ctNull = false;
    private bool $ctAutoIncrement = false;
    private bool $ctPrimaryKey = false;
    private string $ctDefaultValue = '';
    private bool $ctDefaultValueZero = false;
    private string $ctAfter = '';

    private function clean(): void
    {
        $this->currentType        = self::TYPE_VARCHAR;
        $this->ctTamanho          = 45;
        $this->ctPrecision        = 10;
        $this->ctScale            = 2;
        $this->ctNull             = false;
        $this->ctAutoIncrement    = false;
        $this->ctPrimaryKey       = false;
        $this->ctDefaultValue     = '';
        $this->ctDefaultValueZero = false;
        $this->ctAfter            = '';
        $this->sEngine            = 'MyISAM';
    }

    public function cleanAll(): self
    {
        $this->clean();
        $this->sCreateColumns = [];
        $this->clearQueryState();
        return $this;
    }

    public function varchar(int $size = 45): self
    {
        $this->currentType = self::TYPE_VARCHAR;
        $this->ctTamanho = $size;
        return $this;
    }

    public function char(int $size = 1): self
    {
        $this->currentType = self::TYPE_CHAR;
        $this->ctTamanho = $size;
        return $this;
    }

    public function int(): self
    {
        $this->currentType = self::TYPE_INT;
        return $this;
    }

    public function tinyint(): self
    {
        $this->currentType = self::TYPE_TINYINT;
        return $this;
    }

    public function bigint(): self
    {
        $this->currentType = self::TYPE_BIGINT;
        return $this;
    }

    public function text(): self
    {
        $this->currentType = self::TYPE_TEXT;
        return $this;
    }

    public function mediumtext(): self
    {
        $this->currentType = self::TYPE_MEDIUMTEXT;
        return $this;
    }

    public function longtext(): self
    {
        $this->currentType = self::TYPE_LONGTEXT;
        return $this;
    }

    public function blob(): self
    {
        $this->currentType = self::TYPE_BLOB;
        return $this;
    }

    public function datetime(): self
    {
        $this->currentType = self::TYPE_DATETIME;
        return $this;
    }

    public function decimal(int $precision = 10, int $scale = 2): self
    {
        $this->currentType = self::TYPE_DECIMAL;
        $this->ctPrecision = $precision;
        $this->ctScale = $scale;
        return $this;
    }

    public function json(): self
    {
        $this->currentType = self::TYPE_JSON;
        return $this;
    }

    public function null(): self
    {
        $this->ctNull = true;
        return $this;
    }

    public function autoIncrement(): self
    {
        $this->ctAutoIncrement = true;
        return $this;
    }

    public function primaryKey(): self
    {
        $this->ctPrimaryKey = true;
        return $this;
    }

    public function varcharSize(int $value = 45): self
    {
        $this->ctTamanho = $value;
        return $this;
    }

    public function defaultValue(string $value): self
    {
        $this->ctDefaultValue = $value;
        return $this;
    }

    public function defaultValueZero(): self
    {
        $this->ctDefaultValueZero = true;
        return $this;
    }

    public function after(string $value): self
    {
        $this->ctAfter = $value;
        return $this;
    }

    public function engine(string $name): self
    {
        $this->sEngine = $name;
        return $this;
    }

    public function add(string $name): self
    {
        $sql = "`" . trim($name, "`") . "`";

        switch ($this->currentType) {
            case self::TYPE_INT:        $sql .= ' INT'; break;
            case self::TYPE_TINYINT:    $sql .= ' TINYINT'; break;
            case self::TYPE_BIGINT:     $sql .= ' BIGINT'; break;
            case self::TYPE_TEXT:       $sql .= ' TEXT'; break;
            case self::TYPE_MEDIUMTEXT: $sql .= ' MEDIUMTEXT'; break;
            case self::TYPE_LONGTEXT:   $sql .= ' LONGTEXT'; break;
            case self::TYPE_BLOB:       $sql .= ' BLOB'; break;
            case self::TYPE_DATETIME:   $sql .= ' DATETIME'; break;
            case self::TYPE_JSON:       $sql .= ' JSON'; break;
            case self::TYPE_CHAR:       $sql .= ' CHAR(' . $this->ctTamanho . ')'; break;
            case self::TYPE_DECIMAL:    $sql .= ' DECIMAL(' . $this->ctPrecision . ',' . $this->ctScale . ')'; break;
            default:                    $sql .= ' VARCHAR(' . $this->ctTamanho . ')'; break;
        }

        if (empty($this->ctDefaultValue)) {
            if ($this->ctDefaultValueZero) {
                $sql .= ' DEFAULT 0 NOT NULL';
            } else {
                $sql .= ($this->ctNull) ? ' DEFAULT NULL' : ' NOT NULL';
            }
        } else {
            $escapedValue = mysqli_real_escape_string($this->sConecta, $this->ctDefaultValue);
            $sql .= " DEFAULT '{$escapedValue}' NOT NULL";
        }

        if ($this->ctAutoIncrement) $sql .= ' AUTO_INCREMENT';
        if ($this->ctPrimaryKey)    $sql .= ' PRIMARY KEY';

        if (!empty($this->ctAfter)) {
            $sql .= ' AFTER `' . trim($this->ctAfter, "`") . '`';
        }

        $this->sCreateColumns[] = $sql;
        $this->clean();
        return $this;
    }

    public function create(): self
    {
        try {
            if (empty($this->sCreateColumns)) return $this;

            $columns = implode(', ', $this->sCreateColumns);
            $tableName = "`" . trim($this->getTable(), "`") . "`";

            $txt = sprintf(
                'CREATE TABLE IF NOT EXISTS %s (%s) ENGINE=%s DEFAULT CHARSET=%s COLLATE=%s_general_ci;',
                $tableName, $columns, $this->sEngine, $this->sCharset, $this->sCharset
            );

            mysqli_query($this->sConecta, $txt);
            $this->sFechaResult = false;
            $this->cleanAll();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
        return $this;
    }

    public const ALTER_ADD = 'add';
    public const ALTER_MODIFY = 'modify';

    public function alter(string $type = self::ALTER_ADD): self
    {
        try {
            if (empty($this->sCreateColumns)) return $this;

            $tableName = "`" . trim($this->getTable(), "`") . "`";
            $type = strtolower($type);

            if ($type === self::ALTER_ADD) {
                $columns = 'ADD COLUMN ' . implode(', ADD COLUMN ', $this->sCreateColumns);
            } elseif ($type === self::ALTER_MODIFY) {
                $columns = 'MODIFY COLUMN ' . implode(', MODIFY COLUMN ', $this->sCreateColumns);
            }

            if (isset($columns)) {
                $txt = sprintf('ALTER TABLE %s %s', $tableName, $columns);
                mysqli_query($this->sConecta, $txt);
            }

            $this->sFechaResult = false;
            $this->cleanAll();
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
        return $this;
    }

    public function columnExists(string $column): bool
    {
        $exists = false;
        try {
            $tableClean  = mysqli_real_escape_string($this->sConecta, $this->getTable());
            $columnClean = mysqli_real_escape_string($this->sConecta, $column);

            $sql = sprintf(
                "SELECT COUNT(*) AS count1 FROM information_schema.columns WHERE table_name='%s' AND column_name='%s'",
                $tableClean, $columnClean
            );

            if ($result = mysqli_query($this->sConecta, $sql)) {
                $row = mysqli_fetch_assoc($result);
                if ((int)$row['count1'] > 0) $exists = true;
                mysqli_free_result($result);
            }
        } catch (\mysqli_sql_exception $ex) {
            $this->log($ex);
        }
        return $exists;
    }
}
