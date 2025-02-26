<?php

namespace Core;

use PDO;
use PDOException;
use RuntimeException;

final class Migration
{
    private PDO $pdo;
    private string $table;
    private array $columns = [];
    private array $indexes = [];
    private array $foreignKeys = [];

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function id(): self
    {
        $this->columns[] = "id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $column, int $length = 255, bool $nullable = false): self
    {
        $this->columns[] = "$column VARCHAR($length) " . ($nullable ? "NULL" : "NOT NULL");
        return $this;
    }

    public function text(string $column, bool $nullable = false): self
    {
        $this->columns[] = "$column TEXT " . ($nullable ? "NULL" : "NOT NULL");
        return $this;
    }

    public function integer(string $column, bool $unsigned = true, bool $nullable = false): self
    {
        $unsignedPart = $unsigned ? "UNSIGNED" : "";
        $this->columns[] = "$column INT $unsignedPart " . ($nullable ? "NULL" : "NOT NULL");
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    public function nullable(): self
    {
        if (!empty($this->columns)) {
            $this->columns[count($this->columns) - 1] = str_replace("NOT NULL", "NULL", $this->columns[count($this->columns) - 1]);
        }
        return $this;
    }

    public function unique(): self
    {
        if (!empty($this->columns)) {
            $this->columns[count($this->columns) - 1] .= " UNIQUE";
        }
        return $this;
    }

    public function index(string $column): self
    {
        $this->indexes[] = "INDEX ($column)";
        return $this;
    }

    public function foreign(
        string $column,
        string $referencesTable,
        string $referencesColumn,
        string $onDelete = "CASCADE",
        string $onUpdate = "CASCADE"
    ): self {
        $this->foreignKeys[] = "FOREIGN KEY ($column) REFERENCES $referencesTable($referencesColumn) ON DELETE $onDelete ON UPDATE $onUpdate";
        return $this;
    }

    public function create(): bool
    {
        if (empty($this->table) || empty($this->columns)) {
            throw new RuntimeException("Table name and columns must be defined.");
        }

        $sqlParts = array_merge($this->columns, $this->indexes, $this->foreignKeys);
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (" . implode(
                ", ",
                $sqlParts
            ) . ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        try {
            return $this->pdo->exec($sql) !== false;
        } catch (PDOException $e) {
            throw new RuntimeException("Table creation failed: " . $e->getMessage());
        }
    }

    public function drop(): bool
    {
        $sql = "DROP TABLE IF EXISTS {$this->table};";
        try {
            return $this->pdo->exec($sql) !== false;
        } catch (PDOException $e) {
            throw new RuntimeException("Table drop failed: " . $e->getMessage());
        }
    }
}
