<?php

declare(strict_types=1);

namespace App;

class FileUpload
{
    private array $config;
    private array $errors = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function upload(array $file, string $uploadPath = null): ?array
    {
        $this->errors = [];

        if (!$this->validateFile($file)) {
            return null;
        }

        $uploadPath = $uploadPath ?? $this->config['path'];
        $fileInfo = $this->generateFileInfo($file);
        $directory = $this->createDirectoryStructure($uploadPath, $fileInfo['year'], $fileInfo['month']);

        $fullPath = $directory . '/' . $fileInfo['filename'];
        $relativePath = $this->getRelativePath($fullPath);

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $this->errors[] = 'Failed to move uploaded file';

            return null;
        }

        $this->setFilePermissions($fullPath);

        return [
            'filename' => $fileInfo['filename'],
            'original_name' => $file['name'],
            'mime_type' => $fileInfo['mime_type'],
            'size' => $file['size'],
            'path' => $relativePath,
            'full_path' => $fullPath,
        ];
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getLastError(): ?string
    {
        return end($this->errors) ?: null;
    }

    private function validateFile(array $file): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);

            return false;
        }

        if ($file['size'] > $this->config['max_size']) {
            $this->errors[] = 'File size exceeds maximum allowed size';

            return false;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->config['allowed_types'], true)) {
            $this->errors[] = 'File type not allowed';

            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_extensions'], true)) {
            $this->errors[] = 'File extension not allowed';

            return false;
        }

        if (!$this->isValidImage($file['tmp_name'])) {
            $this->errors[] = 'Invalid image file';

            return false;
        }

        return true;
    }

    private function isValidImage(string $filePath): bool
    {
        $imageInfo = @getimagesize($filePath);

        return $imageInfo !== false;
    }

    private function generateFileInfo(array $file): array
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        $uuid = $this->generateUuid();
        $filename = $uuid . '.' . $extension;

        return [
            'filename' => $filename,
            'uuid' => $uuid,
            'extension' => $extension,
            'mime_type' => $mimeType,
            'year' => date('Y'),
            'month' => date('m'),
        ];
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    private function createDirectoryStructure(string $basePath, string $year, string $month): string
    {
        $directory = $basePath . '/' . $year . '/' . $month;

        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new \RuntimeException("Could not create directory: {$directory}");
            }
        }

        return $directory;
    }

    private function getRelativePath(string $fullPath): string
    {
        $basePath = rtrim($this->config['path'], '/') . '/';

        return str_replace($basePath, '', $fullPath);
    }

    private function setFilePermissions(string $filePath): void
    {
        chmod($filePath, 0644);
    }

    private function getUploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File size exceeds maximum allowed size',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            default => 'Unknown upload error',
        };
    }

    public static function delete(string $filePath): bool
    {
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }

        return true;
    }
}