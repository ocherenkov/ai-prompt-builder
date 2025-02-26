<?php

namespace Core;

final class Config
{
    private static array $configs = [];

    private function __construct()
    {
    }

    public static function load(string $file): void
    {
        $path = __DIR__ . "/../config/{$file}.php";
        if (file_exists($path)) {
            self::$configs[$file] = require $path;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        [$file, $configKey] = explode('.', $key, 2) + [null, null];

        if (!isset(self::$configs[$file])) {
            self::load($file);
        }

        return self::$configs[$file][$configKey] ?? $default;
    }
}
