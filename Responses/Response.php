<?php

namespace Lumin\Responses;

use Lumin\Responses\Traits\HttpStatusCodeTrait;

class Response {
    use HttpStatusCodeTrait;

    public function json($data, $code = self::HTTP_OK) {
        header('Content-Type: application/json');
        http_response_code($code);

        return json_encode($data);
    }
}