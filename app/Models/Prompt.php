<?php

namespace App\Models;

use Core\Model;

/**
 * App\Models\Prompt
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $user_id
 * @property int $category_id
 * @property int $version
 *
 */
class Prompt extends Model
{
    protected static string $table = 'prompts';

    public function user(): ?Model
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): ?Model
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function ratings(): array
    {
        return $this->hasMany(PromptRating::class, 'prompt_id');
    }

    public function combinations(): array
    {
        return $this->hasMany(PromptCombination::class, 'prompt_id');
    }

    public static function findByCategory(int $categoryId): array
    {
        return static::query()
            ->where('category_id', $categoryId)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    public static function findTopRated(int $limit = 10): array
    {
        return static::query()
            ->orderBy('rating', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function incrementVersion(): bool
    {
        return $this->update([
            'version' => ($this->version ?? 0) + 1
        ]);
    }

    public function updateRating(): bool
    {
        $ratings = $this->ratings();
        $count = count($ratings);

        if ($count === 0) {
            return $this->update([
                'rating' => 0,
                'ratings_count' => 0
            ]);
        }

        $sum = array_sum(array_map(static fn($rating) => $rating->rating, $ratings));

        return $this->update([
            'rating' => $sum / $count,
            'ratings_count' => $count
        ]);
    }
}
