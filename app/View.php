<?php

declare(strict_types=1);

namespace App;

class View
{
    private static string $viewPath = __DIR__ . '/Views';
    private static array $globals = [];

    public static function setPath(string $path): void
    {
        self::$viewPath = $path;
    }

    public static function share(string $key, mixed $value): void
    {
        self::$globals[$key] = $value;
    }

    public static function render(string $template, array $data = []): void
    {
        $templatePath = self::$viewPath . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View template not found: {$template}");
        }

        $data = array_merge(self::$globals, $data);
        extract($data, EXTR_SKIP);

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        echo $content;
    }

    public static function escape(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function url(string $path): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/');

        return $baseUrl . '/' . ltrim($path, '/');
    }

    public static function asset(string $path): string
    {
        return self::url($path);
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return Session::flash("old_{$key}") ?? $default;
    }

    public static function error(string $key): ?string
    {
        $errors = Session::flash('errors') ?? [];

        return $errors[$key] ?? null;
    }

    public static function csrf(): string
    {
        return Csrf::field();
    }

    public static function csrfMeta(): string
    {
        return Csrf::meta();
    }
}