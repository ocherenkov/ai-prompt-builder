<?php

namespace App\Controllers;

use App\Models\User;
use Core\Auth;
use Core\Exceptions\JsonEncodingException;
use Core\Exceptions\ValidationException;
use Core\Request;
use Random\RandomException;
use RuntimeException;

class AuthController extends Controller
{

    /**
     * @throws JsonEncodingException
     * @throws ValidationException|RandomException
     */
    public function register(Request $request): false|string
    {
        $data = $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);

        // Check if user already exists
        if (User::findByEmail($data['email'])) {
            throw new RuntimeException('User with this email already exists');
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT)
        ]);

        // Log the user in
        Auth::login($user);

        return $this->response->success([
            'message' => 'Registration successful',
            'user' => $user->toArray()
        ]);
    }

    /**
     * @throws JsonEncodingException
     * @throws ValidationException
     */
    public function login(Request $request): false|string
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'remember' => 'sometimes|boolean'
        ]);

        if (!Auth::attempt($data['email'], $data['password'], $data['remember'] ?? false)) {
            throw new RuntimeException('Invalid credentials');
        }

        return $this->response->success([
            'message' => 'Login successful',
            'user' => Auth::user()?->toArray()
        ]);
    }

    /**
     * @throws JsonEncodingException
     */
    public function logout(): false|string
    {
        Auth::logout();

        return $this->response->success([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * @throws JsonEncodingException
     */
    public function user(): false|string
    {
        if (Auth::guest()) {
            throw new RuntimeException('Unauthenticated');
        }

        return $this->response->success([
            'user' => Auth::user()?->toArray()
        ]);
    }
}
