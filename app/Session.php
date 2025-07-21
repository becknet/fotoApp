<?php

declare(strict_types=1);

namespace App;

class Session
{
    private static bool $started = false;
    private static array $config = [];

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (headers_sent()) {
            throw new \RuntimeException('Cannot start session: headers already sent');
        }

        self::configureSession();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$started = true;
        self::validateSession();
    }

    private static function configureSession(): void
    {
        $config = self::$config;

        session_name($config['name'] ?? 'FOTO_APP_SESSION');

        session_set_cookie_params([
            'lifetime' => $config['lifetime'] ?? 7200,
            'path' => '/',
            'domain' => '',
            'secure' => $config['cookie_secure'] ?? false,
            'httponly' => $config['cookie_httponly'] ?? true,
            'samesite' => $config['cookie_samesite'] ?? 'Lax',
        ]);

        ini_set('session.gc_maxlifetime', (string) ($config['lifetime'] ?? 7200));
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
    }

    private static function validateSession(): void
    {
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
            $_SESSION['last_regenerated'] = time();

            return;
        }

        $sessionLifetime = self::$config['lifetime'] ?? 7200;
        $regenerateInterval = 300; // 5 minutes

        if (time() - $_SESSION['created_at'] > $sessionLifetime) {
            self::destroy();

            return;
        }

        if (time() - $_SESSION['last_regenerated'] > $regenerateInterval) {
            self::regenerateId();
        }
    }

    public static function regenerateId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['last_regenerated'] = time();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();

        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();

        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            self::put("_flash_{$key}", $value);

            return $value;
        }

        $flashKey = "_flash_{$key}";
        $value = self::get($flashKey);
        self::forget($flashKey);

        return $value;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
            self::$started = false;
        }
    }

    public static function all(): array
    {
        self::start();

        return $_SESSION;
    }
}