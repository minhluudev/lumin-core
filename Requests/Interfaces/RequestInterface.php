<?php

namespace Lumin\Requests\Interfaces;

interface RequestInterface {
    /**
     * Get the HTTP request method.
     *
     * This method retrieves the HTTP request method used for the current request.
     * It returns the method in uppercase format.
     *
     * @return string The HTTP request method in uppercase.
     */
    public function method(): string;

    /**
     * Get the URI of the request.
     *
     * This method retrieves the URI of the current request. It returns the URI
     * in string format.
     *
     * @return string The URI of the request.
     */
    public function uri(): string;
}