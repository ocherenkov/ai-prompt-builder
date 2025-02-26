<?php

namespace Core;

use Core\Exceptions\ValidationException;
use JsonException;

final class Request
{
    private array $data;
    private string $method;
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->data = array_merge(
            $_GET,
            $_POST,
            $this->parseJsonInput()
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function input(string $key, mixed $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->data, array_flip($keys));
    }

    /**
     * @throws ValidationException
     */
    public function validate(array $rules): array
    {
        return Validator::validate($this->data, $rules);
    }

    private function parseJsonInput(): array
    {
        $input = file_get_contents('php://input');

        if (empty($input)) {
            return [];
        }

        try {
            return json_decode($input, true, 512, JSON_THROW_ON_ERROR) ?? [];
        } catch (JsonException) {
            return [];
        }
    }

}
