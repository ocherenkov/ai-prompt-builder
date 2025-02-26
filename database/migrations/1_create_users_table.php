<?php

use Core\Migration;

return new class {
    public function up(): void
    {
        (new Migration)
            ->table('users')
            ->id()
            ->string('name')
            ->string('email')->unique()
            ->string('password')
            ->string('remember_token', 100)->nullable()
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        (new Migration)->table('users')->drop();
    }
};