<?php

namespace Lumin\Routing;

use Lumin\Routing\Interfaces\RouterInterface;

class Router extends RouteResolve implements RouterInterface {
    public function prefix(string $name, mixed $callback, array $middlewares = []): void {
        $previousPrefix = $this->prefix;
        if ($name && $name !== '/') {
            $prefix       = trim($name, '/');
            $this->prefix .= "/$prefix";
        }

        $this->middlewares[$this->prefix] = array_merge($this->middlewares[$this->prefix] ?? [], $middlewares);
        call_user_func($callback);
        $this->prefix = $previousPrefix;
    }

    public function group(mixed $callback, array $middlewares = []): void {
        $this->middlewares[$this->prefix] = array_merge($this->middlewares[$this->prefix] ?? [], $middlewares);
        call_user_func($callback);
    }

    public function get(string $path, mixed $action, array $middlewares = []): void {
        $this->mapPathAndMiddleware(self::GET, $path, $action, $middlewares);
    }

    public function post(string $path, mixed $action, array $middlewares = []): void {
        $this->mapPathAndMiddleware(self::POST, $path, $action, $middlewares);
    }

    public function put(string $path, mixed $action, array $middlewares = []): void {
        $this->mapPathAndMiddleware(self::PUT, $path, $action, $middlewares);
    }

    public function delete(string $path, mixed $action, array $middlewares = []): void {
        $this->mapPathAndMiddleware(self::DELETE, $path, $action, $middlewares);
    }
}