<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository implements RepositoryInterface
{
    public function all(): array
    {
        return Category::all();
    }

    public function find(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): mixed
    {
        // TODO: Implement create() method.
    }

    public function update(int $id, array $data): bool
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id): bool
    {
        // TODO: Implement delete() method.
    }
}
