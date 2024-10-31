<?php

namespace Lumin\Support\Facades;

/**
 * @method static void connectToDatabase()
 *
 * @see \Lumin\Databases\DB
 */
class DB extends Facade {
    protected static function getFacadeAccessor(): string {
        return 'db';
    }
}