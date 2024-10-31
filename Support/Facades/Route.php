<?php

namespace Lumin\Support\Facades;

/**
 * @method static void prefix(string $name, mixed $callback, array $middlewares = [])
 * @method static void group(mixed $callback, array $middlewares = [])
 * @method static void get(string $path, mixed $action, array $middlewares = [])
 * @method static void post(string $path, mixed $action, array $middlewares = [])
 * @method static void put(string $path, mixed $action, array $middlewares = [])
 * @method static void delete(string $path, mixed $action, array $middlewares = [])
 *
 * @see \Lumin\Routing\Router
 */
class Route extends Facade {
    protected static function getFacadeAccessor(): string {
        return 'router';
    }
}