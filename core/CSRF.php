<?php

namespace Core;

class CSRF
{
    private const TOKEN_KEY = '_token';
    private const TOKEN_LENGTH = 32;

    public static function generate(): string
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $_SESSION[self::TOKEN_KEY] = $token;
        return $token;
    }

    public static function verify(?string $token): bool
    {
        if (!$token || !isset($_SESSION[self::TOKEN_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function getToken(): ?string
    {
        return $_SESSION[self::TOKEN_KEY] ?? null;
    }

    public static function removeToken(): void
    {
        unset($_SESSION[self::TOKEN_KEY]);
    }

    public static function getTokenField(): string
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::TOKEN_KEY,
            self::getToken() ?? self::generate()
        );
    }
}
