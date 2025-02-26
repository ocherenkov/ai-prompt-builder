<?php

use App\Controllers\CategoryController;
use App\Controllers\CSRFController;
use App\Controllers\PromptController;
use Core\Router;

// CSRF Token
Router::get('/api/csrf-token', [CSRFController::class, 'token']);

// Prompt Routes
Router::get('/api/prompts', [PromptController::class, 'index']);
Router::get('/api/prompts/{id}', [PromptController::class, 'show']);
Router::post('/api/prompts', [PromptController::class, 'store']);
Router::put('/api/prompts/{id}', [PromptController::class, 'update']);
Router::delete('/api/prompts/{id}', [PromptController::class, 'delete']);

Router::get('/api/prompts/category/{id}', [PromptController::class, 'byCategory']);

// Category Routes
Router::get('/api/categories', [CategoryController::class, 'index']);
//Router::post('/api/categories', 'CategoryController@store');
//Router::put('/api/categories/{id}', 'CategoryController@update');
//Router::delete('/api/categories/{id}', 'CategoryController@delete');
//Router::get('/api/categories/{id}/prompts', 'PromptController@byCategory');
