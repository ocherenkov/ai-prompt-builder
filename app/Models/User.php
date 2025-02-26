<?php

namespace App\Models;

use Core\Model;
use Random\RandomException;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $remember_token
 *
 */
class User extends Model
{
    protected static string $table = 'users';

    protected array $hidden = [
        'password',
        'remember_token'
    ];

    public function prompts(): array
    {
        return $this->hasMany(Prompt::class, 'user_id');
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public static function findByEmail(string $email): ?static
    {
        $attributes = static::query()
            ->where('email', $email)
            ->first();

        return $attributes ? new static($attributes) : null;
    }

    /**
     * @throws RandomException
     */
    public function generateRememberToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['remember_token' => $token]);
        return $token;
    }
}
