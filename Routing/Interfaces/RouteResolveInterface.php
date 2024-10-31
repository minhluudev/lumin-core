<?php

namespace Lumin\Routing\Interfaces;

interface RouteResolveInterface {
    public function mapPathAndMiddleware(string $method, string $path, mixed $action, array $middlewares = []): void;

    public function resolve(): void;
}