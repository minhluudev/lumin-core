<?php

namespace Lumin\Routing\Interfaces;

interface DependencyInjectionInterface {
    public static function resolveDependencies(mixed $objectOrClass): array;
}