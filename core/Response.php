<?php

namespace Core;

use Core\Exceptions\JsonEncodingException;
use JsonException;

final class Response
{
    /**
     * @throws JsonEncodingException
     */
    public function json(array $data, int $status = 200, array $headers = []): false|string
    {
        http_response_code($status);
        header('Content-Type: application/json');

        foreach ($headers as $key => $value) {
            header("$key: $value");
        }

        try {
            exit(json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        } catch (JsonException $e) {
            throw new JsonEncodingException($e->getMessage());
        }
    }

    /**
     * @throws JsonEncodingException
     */
    public function success(array $data, int $status = 200, array $headers = []): false|string
    {
        return $this->json(['success' => true, 'data' => $data], $status, $headers);
    }

    /**
     * @throws JsonEncodingException
     */
    public function error(string $message, int $status = 400, array $headers = []): false|string
    {
        return $this->json(['success' => false, 'error' => $message], $status, $headers);
    }
}
