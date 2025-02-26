<?php

namespace Core\Exceptions;

use Core\ExceptionHandler;
use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message = "Not Found")
    {
        parent::__construct($message, ExceptionHandler::HTTP_NOT_FOUND);
    }
}