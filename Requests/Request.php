<?php

namespace Lumin\Requests;

use Lumin\Requests\Interfaces\RequestInterface;

class Request implements RequestInterface {
    public const GET_METHOD    = 'GET';
    public const POST_METHOD   = 'POST';

    /**
     * Get the URI of the request.
     *
     * This method retrieves the URI of the current request. It returns the URI
     * in string format.
     *
     * @return string The URI of the request.
     */
    public function uri(): string {
        if ($_SERVER['REQUEST_URI'] === '/') {
            return '/';
        }

        return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }

    public function all($args = []): array {
        $method = $this->method();
        $body   = $method === 'get' ? $_GET : $_POST;

        if (!count($args)) {
            return $body;
        }

        $result = [];

        foreach ($args as $attribute) {
            if (isset($body[$attribute])) {
                $result[$attribute] = $body[$attribute];
            }
        }

        return $result;
    }

    /**
     * Get the HTTP request method.
     *
     * This method retrieves the HTTP request method used for the current request.
     * It returns the method in uppercase format.
     *
     * @return string The HTTP request method in uppercase.
     */
    public function method(): string {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool {
        return $this->method() === self::GET_METHOD;
    }

    public function isPost(): bool {
        return $this->method() === self::POST_METHOD;
    }
}