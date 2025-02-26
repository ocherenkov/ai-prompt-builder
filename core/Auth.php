<?php

namespace Core;

use App\Models\User;
use Random\RandomException;

final class Auth
{
    private static ?User $user = null;
    private const REMEMBER_TOKEN_COOKIE = 'remember_token';
    private const REMEMBER_TOKEN_EXPIRY = 30 * 24 * 60 * 60; // 30 days

    /**
     * @throws RandomException
     */
    public static function attempt(string $email, string $password, bool $remember = false): bool
    {
        $user = User::findByEmail($email);

        if (!$user || !$user->verifyPassword($password)) {
            return false;
        }

        self::login($user, $remember);
        return true;
    }

    /**
     * @throws RandomException
     */
    public static function login(User $user, bool $remember = false): void
    {
        self::$user = $user;
        $_SESSION['user_id'] = $user->id;

        if ($remember) {
            $token = $user->generateRememberToken();
            setcookie(
                self::REMEMBER_TOKEN_COOKIE,
                $token,
                time() + self::REMEMBER_TOKEN_EXPIRY,
                '/',
                '',
                true,
                true
            );
        }
    }

    public static function logout(): void
    {
        if (self::check()) {
            self::user()?->update(['remember_token' => null]);
        }

        self::$user = null;
        unset($_SESSION['user_id']);
        setcookie(self::REMEMBER_TOKEN_COOKIE, '', time() - 3600, '/');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function user(): mixed
    {
        if (self::$user !== null) {
            return self::$user;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if ($userId) {
            self::$user = User::find($userId);
            return self::$user;
        }

        $token = $_COOKIE[self::REMEMBER_TOKEN_COOKIE] ?? null;
        if ($token) {
            $user = User::query()->where('remember_token', $token)->first();
            if ($user) {
                self::$user = $user;
                $_SESSION['user_id'] = $user->id;
                return self::$user;
            }
        }

        return null;
    }

    public static function id(): ?int
    {
        return self::user()?->id;
    }

    public static function guest(): bool
    {
        return !self::check();
    }
}
