<?php

declare(strict_types=1);

namespace App\Models;

class Photo extends Model
{
    protected string $table = 'photos';

    public function findById(int $id): ?array
    {
        return $this->find($id);
    }

    public function findByIdWithUser(int $id): ?array
    {
        $stmt = $this->query(
            "SELECT p.*, u.name as user_name 
             FROM {$this->table} p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.id = ? LIMIT 1",
            [$id]
        );

        $result = $stmt->fetch();

        return $result !== false ? $result : null;
    }

    public function getAllPhotos(int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->query(
            "SELECT p.*, u.name as user_name 
             FROM {$this->table} p 
             JOIN users u ON p.user_id = u.id 
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );

        return $stmt->fetchAll();
    }

    public function getPhotosByUser(int $userId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->query(
            "SELECT p.*, u.name as user_name 
             FROM {$this->table} p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.user_id = ? 
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$userId, $limit, $offset]
        );

        return $stmt->fetchAll();
    }

    public function create(
        int $userId,
        string $title,
        ?string $description,
        ?string $location,
        string $filePath,
        string $thumbPath
    ): int {
        $data = [
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'location' => $location,
            'file_path' => $filePath,
            'thumb_path' => $thumbPath,
        ];

        return parent::create($data);
    }

    public function updatePhoto(
        int $photoId,
        string $title,
        ?string $description,
        ?string $location
    ): bool {
        $data = [
            'title' => $title,
            'description' => $description,
            'location' => $location,
        ];

        return $this->update($photoId, $data);
    }

    public function deletePhoto(int $photoId): bool
    {
        return $this->delete($photoId);
    }

    public function getUserPhotoCount(int $userId): int
    {
        $stmt = $this->query(
            "SELECT COUNT(*) as total FROM {$this->table} WHERE user_id = ?",
            [$userId]
        );

        $result = $stmt->fetch();

        return (int) $result['total'];
    }

    public function searchPhotos(string $query, int $limit = 20, int $offset = 0): array
    {
        $searchTerm = "%{$query}%";

        $stmt = $this->query(
            "SELECT p.*, u.name as user_name 
             FROM {$this->table} p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.title LIKE ? 
                OR p.description LIKE ? 
                OR p.location LIKE ? 
                OR u.name LIKE ?
             ORDER BY p.created_at DESC 
             LIMIT ? OFFSET ?",
            [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit, $offset]
        );

        return $stmt->fetchAll();
    }

    public function isOwner(int $photoId, int $userId): bool
    {
        $photo = $this->findById($photoId);

        return $photo && (int) $photo['user_id'] === $userId;
    }
}