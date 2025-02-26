<?php

namespace Database\Seeders;

use App\Models\User;
use Core\Seeder;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
            ]
        ];

        $this->createMany(User::class, $users);
    }
}
