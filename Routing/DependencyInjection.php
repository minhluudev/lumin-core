<?php

namespace Lumin\Routing;

use Lumin\Routing\Interfaces\DependencyInjectionInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Class DependencyInjection
 *
 * This class is responsible for dependency injection.
 *
 * @package Lumin\Routing
 */
class DependencyInjection implements DependencyInjectionInterface {
    /**
     * Resolve the dependencies for a given class or object.
     *
     * This method uses reflection to inspect the constructor of the given class or object
     * and resolves its dependencies. It recursively resolves dependencies for each parameter
     * in the constructor, creating instances of the required classes.
     *
     * @param  mixed  $objectOrClass  The class name or object to resolve dependencies for.
     *
     * @return array An array of resolved dependencies.
     * @throws ReflectionException If the class does not exist or cannot be reflected.
     */
    public static function resolveDependencies(mixed $objectOrClass): array {
        $dependencies = [];
        $reflection   = new ReflectionClass($objectOrClass);

        if (!$reflection->isInstantiable()) {
            return [];
        }

        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return [];
        }

        $params = $constructor->getParameters();

        foreach ($params as $param) {
            $type = (string) $param->getType();
            if ($type && class_exists($type)) {
                $dependencies[] = new $type(...self::resolveDependencies($type));
            }
        }

        return $dependencies;
    }
}