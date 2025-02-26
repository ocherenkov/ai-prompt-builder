<?php

namespace App\Repositories;

use App\Models\Prompt;

class PromptRepository implements RepositoryInterface
{
    public function all(): array
    {
        return Prompt::all();
    }

    public function find(int $id): ?Prompt
    {
        return Prompt::find($id);
    }

    public function findByCategory(int $categoryId): array
    {
        return Prompt::findByCategory($categoryId);
    }

    public function create(array $data): Prompt
    {
        return Prompt::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function incrementVersion(int $id): bool
    {
        return $this->findOrFail($id)->incrementVersion();
    }

    public function updateRating(int $id): bool
    {
        return $this->findOrFail($id)->updateRating();
    }

    public function findOrFail(int $id): Prompt
    {
        return Prompt::findOrFail($id);
    }
}
