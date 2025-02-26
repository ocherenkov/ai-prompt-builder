<?php

namespace App\Controllers;

use Core\Response;

abstract class Controller
{
    protected Response $response;

    public function __construct()
    {
        $this->response = new Response();
    }

    protected function view(string $name, array $data = []): false|string
    {
        extract($data);
        ob_start();
        require_once __DIR__ . '/../../views/' . $name . '.php';
        return ob_get_clean();
    }

    protected function redirect(string $path): false|string
    {
        header('Location: ' . $path);
        return '';
    }
}