<?php

namespace App\Services;

use App\Repositories\PromptCombinationRepository;
use App\Repositories\PromptRepository;
use Core\Auth;
use Core\Exceptions\UnauthorizedException;

class PromptService
{
    private PromptRepository $repository;
    private PromptCombinationRepository $combinationRepository;

    public function __construct()
    {
        $this->repository = new PromptRepository();
        $this->combinationRepository = new PromptCombinationRepository();
    }


    public function getAllPrompts(): array
    {
        return $this->repository->all();
    }

    public function getPromptsByCategory(int $categoryId): array
    {
        return $this->repository->findByCategory($categoryId);
    }

    public function getPromptById(int $id): array
    {
        $prompt = $this->repository->findOrFail($id)->toArray();
        $combinations = $this->combinationRepository->findByPromptId($id);

        $content = [];
        foreach ($combinations as $combination) {
            $content[$combination['name']] = $combination['content'];
        }

        return array_merge($prompt, ['combinations' => $content]);
    }

    public function createPrompt(array $data): array
    {
        $promptData = [
            'content' => $data['raw'],
            'category_id' => $data['category_id'],
            'user_id' => Auth::id(),
            'version' => 1,
        ];

        $prompt = $this->repository->create($promptData);

        if ($prompt) {
            $promptId = $prompt->id;
            $combinationData = [
                ['name' => 'context', 'content' => $data['context']],
                ['name' => 'task', 'content' => $data['task']],
                ['name' => 'format', 'content' => $data['format']],
            ];
            foreach ($combinationData as $combination) {
                $this->combinationRepository->create([
                    'prompt_id' => $promptId,
                    'name' => $combination['name'],
                    'content' => $combination['content'],
                ]);
            }
        }

        return $prompt->toArray();
    }

    /**
     * @throws UnauthorizedException
     */
    public function updatePrompt(int $id, array $data): array
    {
        $prompt = $this->repository->findOrFail($id);
        if ($prompt->user_id !== Auth::id()) {
            throw new UnauthorizedException('You are not authorized to update this prompt');
        }
        $promptData = [
            'content' => $data['raw'],
            'category_id' => $data['category_id'],
        ];

        $this->repository->update($id, $promptData);
        $this->incrementPromptVersion($id);
        $promptCombinations = $this->combinationRepository->findByPromptId($id);
        foreach ($promptCombinations as $combination) {
            $this->combinationRepository->update($combination['id'], [
                'content' => $data[$combination['name']],
            ]);
        }
        return $this->repository->findOrFail($id)->toArray();
    }

    public function deletePrompt(int $id): bool
    {
        $this->repository->findOrFail($id);
        return $this->repository->delete($id);
    }

    private function incrementPromptVersion(int $id): void
    {
        $this->repository->incrementVersion($id);
    }
}
