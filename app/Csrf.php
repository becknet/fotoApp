<?php

declare(strict_types=1);

namespace App;

class Csrf
{
    private static string $tokenName = '_token';

    public static function setTokenName(string $name): void
    {
        self::$tokenName = $name;
    }

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::put(self::$tokenName, $token);

        return $token;
    }

    public static function getToken(): string
    {
        $token = Session::get(self::$tokenName);

        if (!$token) {
            $token = self::generateToken();
        }

        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $sessionToken = Session::get(self::$tokenName);

        return $sessionToken && hash_equals($sessionToken, $token);
    }

    public static function field(): string
    {
        $token = self::getToken();
        $name = htmlspecialchars(self::$tokenName, ENT_QUOTES, 'UTF-8');
        $value = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

        return "<input type=\"hidden\" name=\"{$name}\" value=\"{$value}\">";
    }

    public static function meta(): string
    {
        $token = self::getToken();
        $value = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');

        return "<meta name=\"csrf-token\" content=\"{$value}\">";
    }

    public static function verify(): void
    {
        $token = $_POST[self::$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!self::validateToken($token)) {
            http_response_code(419);
            die('CSRF token mismatch');
        }
    }
}