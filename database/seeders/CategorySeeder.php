<?php

namespace Database\Seeders;

use App\Models\Category;
use Core\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General',
            ],
            [
                'name' => 'Customer Support',
            ],
            [
                'name' => 'Image Generation',
            ],
            [
                'name' => 'Creative',
            ],
        ];

        $this->createMany(Category::class, $categories);
    }
}
