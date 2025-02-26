<?php

namespace App\Models;

use Core\Model;

class PromptRating extends Model
{
    protected static string $table = 'prompt_ratings';

    protected array $casts = [
        'id' => 'integer',
        'prompt_id' => 'integer',
        'rating' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    public function prompt()
    {
        return $this->belongsTo(Prompt::class, 'prompt_id');
    }
}
