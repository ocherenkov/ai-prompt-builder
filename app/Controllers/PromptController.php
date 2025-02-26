<?php

namespace App\Controllers;

use App\Services\PromptService;
use Core\Exceptions\JsonEncodingException;
use Core\Exceptions\ValidationException;
use Core\Request;
use Core\Response;

class PromptController extends Controller
{
    private PromptService $promptService;

    public function __construct()
    {
        parent::__construct();
        $this->promptService = new PromptService();
    }

    /**
     * Get all prompts
     * @throws JsonEncodingException
     */
    public function index(): false|string
    {
        return $this->response->success(
            $this->promptService->getAllPrompts()
        );
    }

    /**
     * Get prompt by category
     * @throws JsonEncodingException
     */
    public function byCategory(Request $request, Response $response, int $categoryId): false|string
    {
        return $this->response->success(
            $this->promptService->getPromptsByCategory($categoryId)
        );
    }

    /**
     * Get prompt by ID
     * @throws JsonEncodingException
     */
    public function show(Request $request, Response $response, int $id): false|string
    {
        return $this->response->success(
            $this->promptService->getPromptById($id)
        );
    }

    /**
     * Create new prompt
     * @throws ValidationException
     * @throws JsonEncodingException
     */
    public function store(Request $request): false|string
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|min:1|max:255',
            'raw' => 'required|min:1',
            'category_id' => 'required|integer',
            'task' => 'min:1',
            'context' => 'min:1',
            'format' => 'min:1',
        ]);

        return $this->response->success(
            $this->promptService->createPrompt($validatedData),
            201
        );
    }

    /**
     * Update existing prompt
     * @throws ValidationException
     * @throws JsonEncodingException
     */
    public function update(Request $request, Response $response, int $id): false|string
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|min:1|max:255',
            'raw' => 'required|min:1',
            'category_id' => 'required|integer',
            'task' => 'min:1',
            'context' => 'min:1',
            'format' => 'min:1',
        ]);

        return $this->response->success(
            $this->promptService->updatePrompt($id, $validatedData)
        );
    }

    /**
     * Delete prompt
     * @throws JsonEncodingException
     */
    public function delete(Request $request, Response $response, int $id): false|string
    {
        $this->promptService->deletePrompt($id);
        return $this->response->success([
            'message' => 'Prompt deleted successfully.'
        ]);
    }
}
