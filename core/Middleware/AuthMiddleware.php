<?php

namespace Core\Middleware;

use Core\Auth;
use Core\ExceptionHandler;
use Core\Exceptions\UnauthorizedException;
use Core\Request;

class AuthMiddleware implements MiddlewareInterface
{
    /**
     * @throws UnauthorizedException
     */
    public function handle(Request $request): void
    {
        if (Auth::guest()) {
            throw new UnauthorizedException('Unauthorized', ExceptionHandler::HTTP_UNAUTHORIZED);
        }
    }
}
