<?php

namespace Lumin\Schemas\Interfaces;

interface SchemaInterface {
    public function create(string $tableName, mixed $callback): void;

    public function table(string $tableName, mixed $callback): void;

    public function dropIfExists(string $table): void;
}