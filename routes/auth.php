<?php

use App\Controllers\AuthController;
use Core\Middleware\AuthMiddleware;
use Core\Middleware\GuestMiddleware;
use Core\Router;

// Guest routes
Router::post('/api/auth/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
Router::post('/api/auth/login', [AuthController::class, 'login'], [GuestMiddleware::class]);

// Auth routes
Router::post('/api/auth/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
Router::get('/api/auth/user', [AuthController::class, 'user'], [AuthMiddleware::class]);
