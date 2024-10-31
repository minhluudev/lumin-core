<?php

namespace Lumin\Log;

use Lumin\Helper;
use Lumin\Log\Interfaces\LogManagerInterface;

class LogManager implements LogManagerInterface {
    public function info($message): void {
        $content = "[".date('Y-m-d H:i:s')."] INFO: ".json_encode($message).PHP_EOL;
        $this->fileWrite($content);
    }

    private function fileWrite($content): void {
        $fileName = date('Y-m-d').'.log';
        $filePath = Helper::basePath("/logs/$fileName");
        $file     = file_exists($filePath) ? fopen($filePath, 'a') : fopen($filePath, 'w');

        if ($file) {
            fwrite($file, $content);
            fclose($file);
        }
    }

    public function warning($message): void {
        $content = "[".date('Y-m-d H:i:s')."] WARNING: ".json_encode($message).PHP_EOL;
        $this->fileWrite($content);
    }

    public function error($message): void {
        $content = "[".date('Y-m-d H:i:s')."] ERROR: ".json_encode($message).PHP_EOL;
        $this->fileWrite($content);
    }

    public function debug($message): void {
        $content = "[".date('Y-m-d H:i:s')."] DEBUG: ".json_encode($message).PHP_EOL;
        $this->fileWrite($content);
    }
}