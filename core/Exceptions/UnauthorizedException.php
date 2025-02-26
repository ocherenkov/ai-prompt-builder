<?php

namespace Core\Exceptions;

use Core\ExceptionHandler;
use Exception;

class UnauthorizedException extends Exception
{
    public function __construct(string $message = 'Unauthorized', int $code = ExceptionHandler::HTTP_UNAUTHORIZED)
    {
        parent::__construct($message, $code);
    }
}
