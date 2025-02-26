<?php

namespace Core;

abstract class Seeder
{
    /**
     * Run the seeder.
     */
    abstract public function run(): void;

    /**
     * Create multiple records.
     */
    protected function createMany(string $modelClass, array $records): void
    {
        foreach ($records as $record) {
            $modelClass::create($record);
        }
    }
}
