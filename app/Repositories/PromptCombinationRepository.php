<?php

namespace App\Repositories;

use App\Models\PromptCombination;

class PromptCombinationRepository implements RepositoryInterface
{
    public function findByPromptId(int $promptId): array
    {
        return PromptCombination::query()
            ->where('prompt_id', $promptId)
            ->get();
    }

    public function all(): array
    {
        return PromptCombination::all();
    }

    public function find(int $id): mixed
    {
        // TODO: Implement find() method.
    }

    public function create(array $data): mixed
    {
        return PromptCombination::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function findOrFail(int $id): PromptCombination
    {
        return PromptCombination::findOrFail($id);
    }
}
