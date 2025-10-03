<?php

namespace Celovel\Database;

use PDO;
use PDOException;

class Connection
{
    protected ?PDO $pdo = null;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = $this->createConnection();
        }

        return $this->pdo;
    }

    protected function createConnection(): PDO
    {
        $dsn = $this->getDsn();
        $options = $this->getOptions();

        try {
            return new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw new \Exception("Database connection failed: " . $e->getMessage());
        }
    }

    protected function getDsn(): string
    {
        $driver = $this->config['driver'] ?? 'mysql';
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? null;
        $database = $this->config['database'] ?? '';

        $dsn = "{$driver}:host={$host}";
        
        if ($port) {
            $dsn .= ";port={$port}";
        }
        
        if ($database) {
            $dsn .= ";dbname={$database}";
        }

        return $dsn;
    }

    protected function getOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    public function query(string $sql, array $bindings = []): array
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $bindings = []): int
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->rowCount();
    }

    public function lastInsertId(): string
    {
        return $this->getPdo()->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->getPdo()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getPdo()->commit();
    }

    public function rollback(): bool
    {
        return $this->getPdo()->rollback();
    }
}
