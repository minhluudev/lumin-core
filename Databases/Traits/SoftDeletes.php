<?php

namespace Lumin\Databases\Traits;

trait SoftDeletes {
    public static function isCheckDeleted(): bool {
        return true;
    }
}