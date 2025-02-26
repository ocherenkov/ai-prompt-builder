<?php

namespace Core\Exceptions;

use Core\ExceptionHandler;
use Exception;

class RateLimitException extends Exception
{
    public function __construct(public readonly int $retryAfter)
    {
        parent::__construct("Rate limit exceeded. Try again later.", ExceptionHandler::HTTP_TOO_MANY_REQUESTS);
    }

    public function getRetryAfter(): int
    {
        return $this->retryAfter;
    }
}