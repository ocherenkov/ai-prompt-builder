<?php

namespace Core\Exceptions;

use Core\ExceptionHandler;
use Exception;

class JsonEncodingException extends Exception
{
    public function __construct(string $message = "Failed to encode JSON response.")
    {
        parent::__construct($message, ExceptionHandler::HTTP_INTERNAL_SERVER_ERROR);
    }

}