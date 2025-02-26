<?php

namespace Core\Middleware;

use Core\Request;

interface MiddlewareInterface
{
    public function handle(Request $request): void;
}
