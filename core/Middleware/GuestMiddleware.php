<?php

namespace Core\Middleware;

use Core\Auth;
use Core\ExceptionHandler;
use Core\Request;
use RuntimeException;

class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request): void
    {
        if (Auth::check()) {
            throw new RuntimeException('Already authenticated', ExceptionHandler::HTTP_BAD_REQUEST);
        }
    }
}
