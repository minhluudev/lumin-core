<?php

namespace Lumin\Support\Facades;

/**
 * @method static void create(string $tableName, mixed $callback)
 * @method static void table(string $tableName, mixed $callback)
 * @method static void dropIfExists(string $table)
 *
 * @see \Lumin\Schemas\Schema
 */
class Schema extends Facade {
    protected static function getFacadeAccessor(): string {
        return 'schema';
    }

}