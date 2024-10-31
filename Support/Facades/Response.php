<?php

namespace Lumin\Support\Facades;

/**
 * @method static mixed json($data, $code)
 *
 * @see \Lumin\Responses\Response
 */
class Response extends Facade {
    protected static function getFacadeAccessor(): string {
        return 'response';
    }
}