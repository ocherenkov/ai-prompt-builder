<?php

use Core\Migration;

return new class {
    public function up(): void
    {
        (new Migration)
            ->table('categories')
            ->id()
            ->string('name', 100)->index('name')
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        (new Migration)->table('categories')->drop();
    }
};