<?php

declare(strict_types=1);

namespace App\Models;

use App\Database;
use PDO;
use PDOStatement;

abstract class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    protected function find(int $id): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1",
            [$id]
        );

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    protected function findBy(string $column, mixed $value): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1",
            [$value]
        );

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    protected function all(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        return $stmt->fetchAll();
    }

    protected function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $stmt = $this->query(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})",
            $data
        );

        return (int) $this->db->lastInsertId();
    }

    protected function update(int $id, array $data): bool
    {
        $setClause = implode(', ', array_map(fn($key) => "{$key} = :{$key}", array_keys($data)));
        $data['id'] = $id;

        $stmt = $this->query(
            "UPDATE {$this->table} SET {$setClause} WHERE id = :id",
            $data
        );

        return $stmt->rowCount() > 0;
    }

    protected function delete(int $id): bool
    {
        $stmt = $this->query(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );

        return $stmt->rowCount() > 0;
    }

    protected function count(): int
    {
        $stmt = $this->query("SELECT COUNT(*) as total FROM {$this->table}");
        $result = $stmt->fetch();

        return (int) $result['total'];
    }
}