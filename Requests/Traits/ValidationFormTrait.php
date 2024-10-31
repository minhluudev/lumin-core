<?php

namespace Lumin\Requests\Traits;

trait ValidationFormTrait {
    private const RULE_REQUIRED = 'required';
    private const RULE_MAX      = 'max';
    private const RULE_MIN      = 'min';
    private const RULE_LENGTH   = 'length';
    private const RULE_EMAIL    = 'email';
    private const RULE_PASSWORD = 'password';
    private const RULE_EQUAL    = 'equal';
    private const RULE_MATCH    = 'match';

    public function validator($rules, $value, $values): ?array {
        $errors = [];
        foreach ($rules as $rule) {
            $errorMessage = match ($rule['name']) {
                self::RULE_REQUIRED => $this->validateRequired($rule, $value),
                self::RULE_MAX      => $this->validateMax($rule, $value),
                self::RULE_MIN      => $this->validateMin($rule, $value),
                self::RULE_EMAIL    => $this->validateEmail($rule, $value),
                self::RULE_PASSWORD => $this->validatePassword($rule, $value),
                self::RULE_EQUAL    => $this->validateEqual($rule, $value, $values),
                self::RULE_MATCH    => $this->validateMatch($rule, $value),
                default             => null
            };

            if ($errorMessage) {
                $errors[] = $errorMessage;
            }
        }

        return count($errors) ? $errors : null;
    }

    /**
     * [
     *   'name'            =>    'required',
     *   'message'    =>    string | null
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validateRequired(mixed $rule, mixed $value): mixed {
        $message = $rule['message'] ?? 'This field is required';

        return $value ? null : $message;
    }

    /**
     * [
     *   'name'=>'max',
     *   'message'=>string | null,
     *   'size'=>number
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validateMax(mixed $rule, mixed $value): mixed {
        $size    = $rule['size'];
        $message = $rule['message'] ?? "This field must not exceed $size characters";

        if (gettype($value) === 'string' && count($value) > $size) {
            return $message;
        }
        if (gettype($value) === 'integer' && $value > $size) {
            return $message;
        }

        return null;
    }

    /**
     * [
     *   'name'            =>    'min',
     *   'message'    =>    string | null,
     *   'size'            =>    number
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validateMin(mixed $rule, mixed $value): mixed {
        $size    = $rule['size'];
        $message = $rule['message'] ?? "This field must have at least $size characters";

        if (gettype($value) === 'string' && count($value) < $size) {
            return $message;
        }
        if (gettype($value) === 'integer' && $value < $size) {
            return $message;
        }

        return null;
    }

    /**
     * [
     *   'name'            =>    'email',
     *   'message'    =>    string | null
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validateEmail(mixed $rule, mixed $value): mixed {
        $message = $rule['message'] ?? "This email is invalid";

        return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : $message;
    }

    /**
     * [
     *   'name'            =>    'password',
     *   'message'    =>    string | null
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validatePassword(mixed $rule, mixed $value): mixed {
        $pattern = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,255}$/";
        $message = $rule['message'] ?? "This password is invalid";

        return preg_match($pattern, $value) ? null : $message;
    }

    /**
     * [
     *   'name'            =>    'equal',
     *   'message'    =>    string | null,
     *     'field'        =>    string
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     * @param         $values
     *
     * @return mixed
     */
    private function validateEqual(mixed $rule, mixed $value, $values): mixed {
        $message = $rule['message'] ?? "This confirm password is not match";

        return $value === $values[$rule['field']] ? null : $message;
    }

    /**
     * [
     *   'name'            =>    'match',
     *   'message'    =>    string | null,
     *     'pattern'    =>    string
     * ]
     *
     * @param  mixed  $rule
     * @param  mixed  $value
     *
     * @return mixed
     */
    private function validateMatch(mixed $rule, mixed $value): mixed {
        $message = $rule['message'] ?? "This field is not match";

        return preg_match($rule['pattern'], $value) ? null : $message;
    }
}
