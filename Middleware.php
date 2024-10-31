<?php

namespace Lumin;

abstract class Middleware {
    public function __construct() {
        $this->handle();
    }

    /**
     * Handle the middleware logic.
     *
     * This method should be implemented by any class that extends the Middleware class.
     * It contains the logic that will be executed when the middleware is invoked.
     *
     * @return void
     */
    abstract public function handle(): void;

    protected function getSession($key) {
        return App::$app->session->getFlash($key);
    }
}