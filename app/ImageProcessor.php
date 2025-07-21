<?php

declare(strict_types=1);

namespace App;

class ImageProcessor
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function generateThumbnail(string $sourcePath, string $destinationPath, int $size = null): bool
    {
        $size = $size ?? $this->config['thumbnail_size'];

        if (!file_exists($sourcePath)) {
            throw new \RuntimeException("Source file does not exist: {$sourcePath}");
        }

        $imageInfo = getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new \RuntimeException('Invalid image file');
        }

        [$width, $height, $type] = $imageInfo;

        $sourceImage = $this->createImageFromType($sourcePath, $type);
        if ($sourceImage === false) {
            throw new \RuntimeException('Could not create image resource');
        }

        $thumbnailDimensions = $this->calculateThumbnailDimensions($width, $height, $size);

        $thumbnail = imagecreatetruecolor($thumbnailDimensions['width'], $thumbnailDimensions['height']);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            $this->preserveTransparency($thumbnail, $sourceImage, $type);
        }

        $success = imagecopyresampled(
            $thumbnail,
            $sourceImage,
            0,
            0,
            0,
            0,
            $thumbnailDimensions['width'],
            $thumbnailDimensions['height'],
            $width,
            $height
        );

        if (!$success) {
            imagedestroy($sourceImage);
            imagedestroy($thumbnail);

            return false;
        }

        $this->ensureDirectoryExists(dirname($destinationPath));

        $success = $this->saveImageToFile($thumbnail, $destinationPath, $type);

        imagedestroy($sourceImage);
        imagedestroy($thumbnail);

        if ($success) {
            chmod($destinationPath, 0644);
        }

        return $success;
    }

    private function createImageFromType(string $path, int $type): \GdImage|false
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_WEBP => extension_loaded('gd') && function_exists('imagecreatefromwebp')
                ? imagecreatefromwebp($path)
                : false,
            default => false,
        };
    }

    private function saveImageToFile(\GdImage $image, string $path, int $type): bool
    {
        return match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, $path, 85),
            IMAGETYPE_PNG => imagepng($image, $path, 6),
            IMAGETYPE_GIF => imagegif($image, $path),
            IMAGETYPE_WEBP => extension_loaded('gd') && function_exists('imagewebp')
                ? imagewebp($image, $path, 85)
                : false,
            default => false,
        };
    }

    private function calculateThumbnailDimensions(int $originalWidth, int $originalHeight, int $targetSize): array
    {
        if ($originalWidth <= $targetSize && $originalHeight <= $targetSize) {
            return [
                'width' => $originalWidth,
                'height' => $originalHeight,
            ];
        }

        $ratio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            $width = $targetSize;
            $height = (int) round($targetSize / $ratio);
        } else {
            $height = $targetSize;
            $width = (int) round($targetSize * $ratio);
        }

        return [
            'width' => $width,
            'height' => $height,
        ];
    }

    private function preserveTransparency(\GdImage $thumbnail, \GdImage $source, int $type): void
    {
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefill($thumbnail, 0, 0, $transparent);
        } elseif ($type === IMAGETYPE_GIF) {
            $transparentIndex = imagecolortransparent($source);
            if ($transparentIndex >= 0) {
                $transparentColor = imagecolorsforindex($source, $transparentIndex);
                $transparentNew = imagecolorallocate(
                    $thumbnail,
                    $transparentColor['red'],
                    $transparentColor['green'],
                    $transparentColor['blue']
                );
                imagefill($thumbnail, 0, 0, $transparentNew);
                imagecolortransparent($thumbnail, $transparentNew);
            }
        }
    }

    private function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory)) {
            if (!mkdir($directory, 0755, true)) {
                throw new \RuntimeException("Could not create directory: {$directory}");
            }
        }
    }

    public function getImageInfo(string $path): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        $imageInfo = getimagesize($path);
        if ($imageInfo === false) {
            return null;
        }

        [$width, $height, $type] = $imageInfo;

        return [
            'width' => $width,
            'height' => $height,
            'type' => $type,
            'mime' => $imageInfo['mime'],
        ];
    }

    public static function isGdAvailable(): bool
    {
        return extension_loaded('gd');
    }

    public static function isImagickAvailable(): bool
    {
        return extension_loaded('imagick');
    }
}