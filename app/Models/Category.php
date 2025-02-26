<?php

namespace App\Models;

use Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public function prompts(): array
    {
        return $this->hasMany(Prompt::class, 'category_id');
    }
}
