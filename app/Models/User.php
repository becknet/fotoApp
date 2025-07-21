<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    protected string $table = 'users';

    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function createUser(string $name, string $email, string $password): int
    {
        $data = [
            'name' => $name,
            'email' => $email,
            'password_hash' => $this->hashPassword($password),
        ];

        return parent::create($data);
    }

    public function updateProfile(int $userId, string $name, string $email): bool
    {
        $data = [
            'name' => $name,
            'email' => $email,
        ];

        return $this->update($userId, $data);
    }

    public function updatePassword(int $userId, string $password): bool
    {
        $data = [
            'password_hash' => $this->hashPassword($password),
        ];

        return $this->update($userId, $data);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->findById($userId);
        
        if (!$user || !$this->verifyPassword($currentPassword, $user['password_hash'])) {
            return false;
        }

        return $this->updatePassword($userId, $newPassword);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user || !$this->verifyPassword($password, $user['password_hash'])) {
            return null;
        }

        unset($user['password_hash']);

        return $user;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    private function hashPassword(string $password): string
    {
        $cost = (int) ($_ENV['BCRYPT_COST'] ?? 12);

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    public function needsRehash(string $hash): bool
    {
        $cost = (int) ($_ENV['BCRYPT_COST'] ?? 12);

        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $cost]);
    }

    public function getAllUsers(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->query(
            "SELECT id, name, email, created_at FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        return $stmt->fetchAll();
    }
}