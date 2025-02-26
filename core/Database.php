<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

final class Database
{
    private static ?PDO $pdo = null;
    private string $table;
    private array $joins = [];
    private array $conditions = [];
    private array $bindings = [];
    private string|array $columns = '*';
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $groupBy = [];
    private array $having = [];

    /**
     * @throws RuntimeException
     */
    public static function connect(): PDO
    {
        if (!self::$pdo) {
            $config = Config::get('database.connection');
            try {
                self::$pdo = new PDO(
                    "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function table(string $table): Database
    {
        $instance = new self();
        $instance->table = $table;
        return $instance;
    }

    public function select(string|array $columns = '*'): static
    {
        $this->columns = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function where(string $column, mixed $value = null, string $operator = '='): static
    {
        if (is_null($value)) {
            $this->conditions[] = $column; // Raw where condition
        } else {
            $this->conditions[] = "$column $operator ?";
            $this->bindings[] = $value;
        }
        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $placeholders = rtrim(str_repeat('?,', count($values)), ',');
        $this->conditions[] = "$column IN ($placeholders)";
        $this->bindings = array_merge($this->bindings, $values);
        return $this;
    }

    public function whereBetween(string $column, mixed $start, mixed $end): static
    {
        $this->conditions[] = "$column BETWEEN ? AND ?";
        $this->bindings[] = $start;
        $this->bindings[] = $end;
        return $this;
    }

    public function whereNull(string $column): static
    {
        $this->conditions[] = "$column IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): static
    {
        $this->conditions[] = "$column IS NOT NULL";
        return $this;
    }

    public function orWhere(string $column, mixed $value = null, string $operator = '='): static
    {
        if (!empty($this->conditions)) {
            $this->conditions[] = 'OR';
        }
        return $this->where($column, $value, $operator);
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): static
    {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): static
    {
        return $this->join($table, $first, $operator, $second, 'RIGHT');
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $this->orderBy[] = "$column $direction";
        return $this;
    }

    public function groupBy(string|array $columns): static
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    public function having(string $column, mixed $value, string $operator = '='): static
    {
        $this->having[] = "$column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    public static function raw(string $query, array $bindings = []): array
    {
        try {
            $stmt = self::connect()->prepare($query);
            $stmt->execute($bindings);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Raw query failed: " . $e->getMessage());
        }
    }

    public function get(): array
    {
        try {
            $sql = $this->buildSelectQuery();
            return $this->query($sql, $this->bindings)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RuntimeException("Database query failed: " . $e->getMessage());
        }
    }

    public function first()
    {
        return $this->limit(1)->get()[0] ?? null;
    }

    public function insert(array $data): bool
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            return $this->execute($sql, array_values($data));
        } catch (PDOException $e) {
            throw new RuntimeException("Insert failed: " . $e->getMessage());
        }
    }

    public function insertGetId(array $data): int
    {
        try {
            $this->insert($data);
            return (int)self::connect()->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException("Insert failed: " . $e->getMessage());
        }
    }

    public function update(array $data): bool
    {
        if (empty($this->conditions)) {
            throw new RuntimeException("Update requires at least one condition.");
        }

        try {
            $setClause = implode(', ', array_map(static fn($column) => "$column = ?", array_keys($data)));
            $sql = "UPDATE {$this->table} SET $setClause" . $this->buildWhereClause();
            return $this->execute($sql, array_merge(array_values($data), $this->bindings));
        } catch (PDOException $e) {
            throw new RuntimeException("Update failed: " . $e->getMessage());
        }
    }

    public function delete(): bool
    {
        if (empty($this->conditions)) {
            throw new RuntimeException("Delete requires at least one condition.");
        }

        try {
            $sql = "DELETE FROM {$this->table}" . $this->buildWhereClause();
            return $this->execute($sql, $this->bindings);
        } catch (PDOException $e) {
            throw new RuntimeException("Delete failed: " . $e->getMessage());
        }
    }

    private function buildSelectQuery(): string
    {
        $sql = "SELECT {$this->columns} FROM {$this->table}";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->buildWhereClause();

        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }

        if (!empty($this->having)) {
            $sql .= ' HAVING ' . implode(' AND ', $this->having);
        }

        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function buildWhereClause(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        $conditions = [];
        $current = '';

        foreach ($this->conditions as $condition) {
            if ($condition === 'OR') {
                if (!empty($current)) {
                    $conditions[] = $current;
                    $current = '';
                }
                $conditions[] = 'OR';
            } else {
                $current = $current ? "$current AND $condition" : $condition;
            }
        }

        if (!empty($current)) {
            $conditions[] = $current;
        }

        return ' WHERE ' . implode(' ', $conditions);
    }

    private function query(string $sql, array $bindings): PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }

    private function execute(string $sql, array $bindings): bool
    {
        return $this->query($sql, $bindings)->rowCount() > 0;
    }
}
