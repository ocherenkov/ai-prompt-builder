<?php

namespace Core\Exceptions;

use Core\ExceptionHandler;
use Exception;

class ValidationException extends Exception
{
    public function __construct(public readonly array $errors, string $message = "Validation failed")
    {
        parent::__construct($message, ExceptionHandler::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}