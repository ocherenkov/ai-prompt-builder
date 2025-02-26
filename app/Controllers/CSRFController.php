<?php

namespace App\Controllers;

use Core\CSRF;
use Core\Exceptions\JsonEncodingException;

class CSRFController extends Controller
{
    /**
     * @throws JsonEncodingException
     */
    public function token(): false|string
    {
        return $this->response->success([
            'token' => CSRF::generate()
        ]);
    }
}
