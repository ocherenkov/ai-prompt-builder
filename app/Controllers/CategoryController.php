<?php

namespace App\Controllers;

use App\Services\CategoryService;
use Core\Exceptions\JsonEncodingException;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct()
    {
        parent::__construct();
        $this->categoryService = new CategoryService();
    }

    /**
     * Get all prompts
     * @throws JsonEncodingException
     */
    public function index(): false|string
    {
        return $this->response->success(
            $this->categoryService->getAllCategories()
        );
    }
}
