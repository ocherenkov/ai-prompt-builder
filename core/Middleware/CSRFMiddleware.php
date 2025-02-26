<?php

namespace Core\Middleware;

use Core\CSRF;
use Core\ExceptionHandler;
use Core\Request;
use RuntimeException;

class CSRFMiddleware implements MiddlewareInterface
{
    private const array EXCLUDED_METHODS = ['GET', 'HEAD', 'OPTIONS'];

    public function handle(Request $request): void
    {
        if (in_array($request->method(), self::EXCLUDED_METHODS)) {
            return;
        }

        $token = $request->input('_token');


        if (!$token) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        if (!CSRF::verify($token)) {
            throw new RuntimeException('CSRF token mismatch', ExceptionHandler::HTTP_INVALID_CSRF_TOKEN);
        }

        CSRF::removeToken();
    }
}
