<?php

namespace Core;

use Core\Exceptions\JsonEncodingException;
use Core\Exceptions\NotFoundException;
use Core\Exceptions\RateLimitException;
use Core\Exceptions\UnauthorizedException;
use Core\Exceptions\ValidationException;
use Exception;
use JsonException;
use PDOException;

final class ExceptionHandler
{
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_NOT_FOUND = 404;

    public const HTTP_INVALID_CSRF_TOKEN = 419;

    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_BAD_REQUEST = 400;

    /**
     * @throws JsonException
     */

    public static function handle(Exception $e): void
    {
        http_response_code(self::getStatusCode($e));
        self::setHeaders($e);

        exit(self::buildJsonResponse($e));
    }

    private static function getStatusCode(Exception $e): int
    {
        return match (true) {
            $e instanceof UnauthorizedException => self::HTTP_UNAUTHORIZED,
            $e instanceof NotFoundException => self::HTTP_NOT_FOUND,
            $e instanceof ValidationException => self::HTTP_UNPROCESSABLE_ENTITY,
            $e instanceof RateLimitException => self::HTTP_TOO_MANY_REQUESTS,
            $e instanceof PDOException, $e instanceof JsonEncodingException => self::HTTP_INTERNAL_SERVER_ERROR,
            default => self::HTTP_BAD_REQUEST,
        };
    }

    private static function setHeaders(Exception $e): void
    {
        header('Content-Type: application/json');

        if ($e instanceof RateLimitException) {
            header("Retry-After: " . $e->getRetryAfter());
        }
    }

    /**
     * @throws JsonException
     */
    private static function buildJsonResponse(Exception $e): string
    {
        $response = [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'type' => get_class($e),
        ];

        if (Config::get('app.debug', false)) {
            $response['file'] = $e->getFile();
            $response['line'] = $e->getLine();
            $response['trace'] = $e->getTraceAsString();
        }

        if ($e instanceof ValidationException) {
            $response['errors'] = $e->getErrors();
        }

        return json_encode($response, JSON_THROW_ON_ERROR);
    }
}
