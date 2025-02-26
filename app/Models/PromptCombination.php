<?php

namespace App\Models;

use Core\Model;

class PromptCombination extends Model
{
    protected static string $table = 'prompt_combinations';

    public function prompt(): ?Model
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }
}
