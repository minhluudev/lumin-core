<?php

namespace Lumin;

class Controller {
    public function view($view, $params = []): string {
        return View::render($view, $params);
    }
}