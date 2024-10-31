<?php

namespace Lumin\Routing\Interfaces;

interface RouterInterface {
    public function prefix(string $name, mixed $callback, array $middlewares = []): void;

    public function group(mixed $callback, array $middlewares = []): void;

    public function get(string $path, mixed $action, array $middlewares = []): void;

    public function post(string $path, mixed $action, array $middlewares = []): void;

    public function put(string $path, mixed $action, array $middlewares = []): void;

    public function delete(string $path, mixed $action, array $middlewares = []): void;
}