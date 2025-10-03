<?php

namespace Celovel\Database;

use Celovel\Support\ServiceContainer;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $guarded = [];
    protected array $attributes = [];
    protected bool $timestamps = true;
    protected string $createdAt = 'created_at';
    protected string $updatedAt = 'updated_at';

    protected static ?Connection $connection = null;
    protected static ?ServiceContainer $container = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public static function setConnection(Connection $connection): void
    {
        static::$connection = $connection;
    }

    public static function setContainer(ServiceContainer $container): void
    {
        static::$container = $container;
    }

    protected function getConnection(): Connection
    {
        if (static::$connection === null) {
            if (static::$container) {
                static::$connection = static::$container->make(Connection::class);
            } else {
                throw new \Exception('Database connection not set');
            }
        }

        return static::$connection;
    }

    protected function getTable(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $className = get_class($this);
        $className = substr($className, strrpos($className, '\\') + 1);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
    }

    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if ($this->isFillable($key)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    protected function isFillable(string $key): bool
    {
        if (in_array($key, $this->guarded)) {
            return false;
        }

        if (empty($this->fillable)) {
            return true;
        }

        return in_array($key, $this->fillable);
    }

    public function save(): bool
    {
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            if (!isset($this->attributes[$this->createdAt])) {
                $this->attributes[$this->createdAt] = $now;
            }
            $this->attributes[$this->updatedAt] = $now;
        }

        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    protected function insert(): bool
    {
        $attributes = $this->attributes;
        unset($attributes[$this->primaryKey]);

        $columns = implode(',', array_keys($attributes));
        $placeholders = ':' . implode(', :', array_keys($attributes));

        $sql = "INSERT INTO {$this->getTable()} ({$columns}) VALUES ({$placeholders})";

        $result = $this->getConnection()->execute($sql, $attributes);

        if ($result) {
            $this->attributes[$this->primaryKey] = $this->getConnection()->lastInsertId();
        }

        return $result > 0;
    }

    protected function update(): bool
    {
        $id = $this->attributes[$this->primaryKey];
        $attributes = $this->attributes;
        unset($attributes[$this->primaryKey]);

        $setClause = [];
        foreach (array_keys($attributes) as $key) {
            $setClause[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setClause);

        $sql = "UPDATE {$this->getTable()} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $attributes['id'] = $id;

        $result = $this->getConnection()->execute($sql, $attributes);
        return $result > 0;
    }

    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        $sql = "DELETE FROM {$this->getTable()} WHERE {$this->primaryKey} = :id";
        $result = $this->getConnection()->execute($sql, ['id' => $this->attributes[$this->primaryKey]]);
        return $result > 0;
    }

    public static function find($id): ?self
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->getTable()} WHERE {$instance->primaryKey} = :id";
        $result = $instance->getConnection()->query($sql, ['id' => $id]);

        if (empty($result)) {
            return null;
        }

        return $instance->newInstance($result[0]);
    }

    public static function all(): array
    {
        $instance = new static();
        $sql = "SELECT * FROM {$instance->getTable()}";
        $results = $instance->getConnection()->query($sql);

        return array_map(function ($attributes) use ($instance) {
            return $instance->newInstance($attributes);
        }, $results);
    }

    public static function where(string $column, $operator, $value = null): QueryBuilder
    {
        $instance = new static();
        $query = new QueryBuilder($instance->getConnection(), $instance->getTable());
        
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        return $query->where($column, $operator, $value);
    }

    protected function newInstance(array $attributes): self
    {
        $instance = new static();
        $instance->fill($attributes);
        return $instance;
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function toJson(): string
    {
        return json_encode($this->attributes);
    }
}
