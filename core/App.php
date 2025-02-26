<?php

namespace Core;

use Exception;
use JsonException;

final class App
{
    /**
     * @throws JsonException
     */
    public static function run(): void
    {
        try {
            self::loadRoutes();
            Router::handle();
        } catch (Exception $e) {
            ExceptionHandler::handle($e);
        }
    }

    private static function loadRoutes(): void
    {
        require_once __DIR__ . '/../routes/web.php';
    }
}
