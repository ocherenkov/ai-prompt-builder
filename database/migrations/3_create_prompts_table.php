<?php

use Core\Migration;

return new class {
    public function up(): void
    {
        (new Migration)
            ->table('prompts')
            ->id()
            ->string('title')->nullable()->index('title')
            ->text('content')
            ->integer('category_id')->foreign('category_id', 'categories', 'id')->index('category_id')
            ->integer('user_id')->foreign('user_id', 'users', 'id')
            ->integer('version')
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        (new Migration)->table('prompts')->drop();
    }
};