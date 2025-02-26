<?php

namespace Database\Seeders;

use Core\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run all seeders in the correct order.
     */
    public function run(): void
    {
        $seeders = [
            UserSeeder::class,
            CategorySeeder::class,
        ];

        foreach ($seeders as $seeder) {
            echo "Running {$seeder}...\n";
            (new $seeder())->run();
            echo "Completed {$seeder}\n";
        }
    }
}
