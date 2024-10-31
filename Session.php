<?php

namespace Lumin;

class Session {
    protected const FLASH_KEY = 'flash_messages';

    public function __construct() {
        session_start();
        $this->initFlashMessages();
    }

    private function initFlashMessages(): void {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function __destruct() {
        //        $this->removeFlashMessages();
    }

    public function setFlash($key, $message): void {
        $_SESSION[self::FLASH_KEY][$key] = ['value'  => $message, 'remove' => false];
    }

    public function getFlash($key) {
        return $_SESSION[self::FLASH_KEY][$key] ?? null;
    }

    private function removeFlashMessages(): void {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}