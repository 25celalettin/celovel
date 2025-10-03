<?php

namespace Celovel\Database;

class QueryBuilder
{
    protected Connection $connection;
    protected string $table;
    protected array $wheres = [];
    protected array $selects = ['*'];
    protected array $orders = [];
    protected ?int $limit = null;
    protected ?int $offset = null;

    public function __construct(Connection $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    public function select(array $columns): self
    {
        $this->selects = $columns;
        return $this;
    }

    public function where(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'AND'
        ];

        return $this;
    }

    public function orWhere(string $column, $operator, $value = null): self
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'OR'
        ];

        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $placeholders = str_repeat('?,', count($values) - 1) . '?';
        
        $this->wheres[] = [
            'column' => $column,
            'operator' => 'IN',
            'value' => "({$placeholders})",
            'values' => $values,
            'boolean' => 'AND'
        ];

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => strtoupper($direction)
        ];

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array
    {
        $sql = $this->toSql();
        $bindings = $this->getBindings();
        
        return $this->connection->query($sql, $bindings);
    }

    public function first(): ?array
    {
        $results = $this->limit(1)->get();
        return $results[0] ?? null;
    }

    public function count(): int
    {
        $sql = $this->toSql(true);
        $bindings = $this->getBindings();
        
        $result = $this->connection->query($sql, $bindings);
        return (int) $result[0]['count'];
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function insert(array $data): bool
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        
        return $this->connection->execute($sql, $data) > 0;
    }

    public function update(array $data): int
    {
        $setClause = [];
        foreach (array_keys($data) as $key) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);

        $whereClause = $this->buildWhereClause();
        $sql = "UPDATE {$this->table} SET {$setClause} {$whereClause}";
        
        $bindings = array_merge($data, $this->getBindings());
        
        return $this->connection->execute($sql, $bindings);
    }

    public function delete(): int
    {
        $whereClause = $this->buildWhereClause();
        $sql = "DELETE FROM {$this->table} {$whereClause}";
        
        return $this->connection->execute($sql, $this->getBindings());
    }

    protected function toSql(bool $count = false): string
    {
        $select = $count ? 'COUNT(*) as count' : implode(', ', $this->selects);
        $sql = "SELECT {$select} FROM {$this->table}";
        
        $whereClause = $this->buildWhereClause();
        if ($whereClause) {
            $sql .= " {$whereClause}";
        }

        if (!empty($this->orders)) {
            $orderClause = 'ORDER BY ' . implode(', ', array_map(function ($order) {
                return "{$order['column']} {$order['direction']}";
            }, $this->orders));
            $sql .= " {$orderClause}";
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    protected function buildWhereClause(): string
    {
        if (empty($this->wheres)) {
            return '';
        }

        $clause = 'WHERE ';
        $conditions = [];

        foreach ($this->wheres as $index => $where) {
            $boolean = $index === 0 ? '' : " {$where['boolean']} ";
            
            if ($where['operator'] === 'IN') {
                $conditions[] = "{$boolean}{$where['column']} IN {$where['value']}";
            } else {
                $conditions[] = "{$boolean}{$where['column']} {$where['operator']} :{$where['column']}";
            }
        }

        return $clause . implode('', $conditions);
    }

    protected function getBindings(): array
    {
        $bindings = [];

        foreach ($this->wheres as $where) {
            if ($where['operator'] === 'IN') {
                $bindings = array_merge($bindings, $where['values']);
            } else {
                $bindings[$where['column']] = $where['value'];
            }
        }

        return $bindings;
    }
}
