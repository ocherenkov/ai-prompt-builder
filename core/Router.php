<?php

namespace Core;

use Core\Exceptions\NotFoundException;
use Core\Exceptions\RateLimitException;
use Core\Middleware\CSRFMiddleware;
use Exception;
use JsonException;
use RuntimeException;

final class Router
{
    private const METHOD_GET = 'GET';
    private const METHOD_POST = 'POST';
    private const METHOD_PUT = 'PUT';
    private const METHOD_DELETE = 'DELETE';

    private static array $routes = [];
    private static array $globalMiddleware = [];

    public static function get(string $uri, array $handler, array $middleware = []): void
    {
        self::addRoute(self::METHOD_GET, $uri, $handler, $middleware);
    }

    public static function post(string $uri, array $handler, array $middleware = []): void
    {
        $middleware[] = CSRFMiddleware::class;
        self::addRoute(self::METHOD_POST, $uri, $handler, $middleware);
    }

    public static function put(string $uri, array $handler, array $middleware = []): void
    {
        $middleware[] = CSRFMiddleware::class;
        self::addRoute(self::METHOD_PUT, $uri, $handler, $middleware);
    }

    public static function delete(string $uri, array $handler, array $middleware = []): void
    {
        $middleware[] = CSRFMiddleware::class;
        self::addRoute(self::METHOD_DELETE, $uri, $handler, $middleware);
    }

    public static function middleware(array $middleware): void
    {
        self::$globalMiddleware = array_merge(self::$globalMiddleware, $middleware);
    }

    private static function addRoute(string $method, string $uri, array $handler, array $middleware): void
    {
        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * @throws JsonException
     * @throws RateLimitException
     * @throws NotFoundException
     */
    public static function handle(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $request = new Request();

        if (in_array($httpMethod, [self::METHOD_PUT, self::METHOD_DELETE], true)) {
            parse_str(file_get_contents("php://input"), $_REQUEST);
        }

        foreach (self::$routes as $route) {
            if ($route['method'] === $httpMethod && preg_match(
                    '#^' . preg_replace('/{.*?}/', '(\d+)', $route['uri']) . '$#',
                    $uri,
                    $matches
                )) {
                array_shift($matches);
                [$controllerClass, $methodName] = $route['handler'];

                if (!class_exists($controllerClass)) {
                    throw new RuntimeException(
                        "Controller $controllerClass not found",
                        ExceptionHandler::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                try {
                    (new RateLimiter())->check($ip);

                    foreach (self::$globalMiddleware as $middleware) {
                        (new $middleware())->handle($request);
                    }

                    foreach ($route['middleware'] as $middleware) {
                        (new $middleware())->handle($request);
                    }

                    $controllerInstance = new $controllerClass();

                    if (!method_exists($controllerInstance, $methodName)) {
                        throw new RuntimeException(
                            "Method $methodName not found in $controllerClass",
                            ExceptionHandler::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }

                    $controllerInstance->$methodName($request, new Response(), ...$matches);
                } catch (Exception $e) {
                    ExceptionHandler::handle($e);
                }
                return;
            }
        }
        throw new NotFoundException("Route not found");
    }
}
