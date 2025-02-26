<?php

require_once __DIR__ . '/vendor/autoload.php';

use Database\Seeders\DatabaseSeeder;

try {
    echo "Starting database seeding...\n";
    (new DatabaseSeeder())->run();
    echo "Database seeding completed successfully!\n";
} catch (Exception $e) {
    echo "Error during seeding: " . $e->getMessage() . "\n";
    exit(1);
}
