<?php


require_once __DIR__ . '/vendor/autoload.php';

use Core\Database;

$pdo = Database::connect();
$migrationsPath = __DIR__ . '/database/migrations/';

$files = glob($migrationsPath . '*.php');

$command = $argv[1] ?? 'up';

if ($command === 'up') {
    echo "Running migrations...\n";

    foreach ($files as $file) {
        require_once $file;
        $migration = require $file;

        if (method_exists($migration, 'up')) {
            echo "Migrating: " . basename($file) . "\n";
            $migration->up();
            echo "Migration successful!\n";
        }
    }

    echo "All migrations completed.\n";
} elseif ($command === 'down') {
    echo "Rolling back migrations...\n";

    foreach (array_reverse($files) as $file) {
        require_once $file;
        $migration = require $file;

        if (method_exists($migration, 'down')) {
            echo "Rolling back: " . basename($file) . "\n";
            $migration->down();
            echo "Migration rolled back!\n";
        }
    }

    echo "All migrations rolled back.\n";
} else {
    echo "Unknown command. Use 'php migrate.php up' or 'php migrate.php down'.\n";
}
