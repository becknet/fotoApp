<?php

declare(strict_types=1);

return [
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',

    'database' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'foto_app',
        'user' => $_ENV['DB_USER'] ?? 'app_user',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ],
    ],

    'session' => [
        'name' => $_ENV['SESSION_NAME'] ?? 'FOTO_APP_SESSION',
        'lifetime' => (int) ($_ENV['SESSION_LIFETIME'] ?? 7200),
        'cookie_secure' => filter_var($_ENV['SESSION_COOKIE_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'cookie_httponly' => filter_var($_ENV['SESSION_COOKIE_HTTPONLY'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'cookie_samesite' => $_ENV['SESSION_COOKIE_SAMESITE'] ?? 'Lax',
    ],

    'upload' => [
        'max_size' => (int) ($_ENV['UPLOAD_MAX_SIZE'] ?? 10485760), // 10MB
        'path' => $_ENV['UPLOAD_PATH'] ?? 'uploads',
        'thumbnail_size' => (int) ($_ENV['THUMBNAIL_SIZE'] ?? 300),
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    ],

    'security' => [
        'csrf_token_name' => $_ENV['CSRF_TOKEN_NAME'] ?? '_token',
        'bcrypt_cost' => (int) ($_ENV['BCRYPT_COST'] ?? 12),
    ],
];