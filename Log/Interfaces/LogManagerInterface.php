<?php

namespace Lumin\Log\Interfaces;

interface LogManagerInterface {
    public function info($message): void;

    public function warning($message): void;

    public function error($message): void;

    public function debug($message): void;
}