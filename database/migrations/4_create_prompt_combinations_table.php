<?php

use Core\Migration;

return new class {
    public function up(): void
    {
        (new Migration)
            ->table('prompt_combinations')
            ->id()
            ->string('name')->index('name')
            ->text('content')
            ->integer('prompt_id')->foreign('prompt_id', 'prompts', 'id')->index('prompt_id')
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        (new Migration)->table('prompt_combinations')->drop();
    }
};