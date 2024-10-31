<?php

namespace Lumin\Support\Facades;

use Exception;
use Lumin\App;

abstract class Facade {
    protected static array $instances = [];

    /**
     * @throws Exception
     */
    public static function __callStatic(string $method, array $args) {
        $instance = static::getFacadeInstance();

        return $instance->$method(...$args);
    }

    /**
     * @throws Exception
     */
    private static function getFacadeInstance() {
        $facadeAccessor = static::getFacadeAccessor();

        if (!isset(self::$instances[$facadeAccessor])) {
            self::$instances[$facadeAccessor] = App::$app->container->get(static::getFacadeAccessor());
        }

        return self::$instances[$facadeAccessor];
    }

    abstract protected static function getFacadeAccessor(): string;
}