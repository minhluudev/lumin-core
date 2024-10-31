<?php

namespace Lumin\Requests;

use Lumin\Requests\Traits\ValidationFormTrait;

abstract class FormRequest extends Request {
    use ValidationFormTrait;

    private array $errors;

    public function __construct() {
        $this->errors = [];
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function validate(): bool {
        $rules           = $this->rules();
        $valueAttributes = $this->all();
        $this->errors    = [];

        foreach ($rules as $key => $ruleProperties) {
            $error = $this->validator($ruleProperties, $valueAttributes[$key], $valueAttributes);
            if ($error) {
                $this->errors[$key] = $error;
            }
        }

        return !count($this->errors);
    }

    /**
     * Rules to validation form
     * Example
     * [
     *        'field_name' => [
     *                [
     *                    'name'        =>    'required',
     *                'message'    =>    string | null
     *                ],
     *                [
     *                'name'        =>    'max',
     *                'message'    =>    string | null,
     *                'size'        =>    number
     *                ],
     *                [
     *                'name'        =>    'min',
     *                'message'    =>    string | null,
     *                'size'        =>    number
     *                ],
     *                [
     *                  'name'        =>    'email',
     *                  'message'    =>    string | null
     *                ],
     *                [
     *                  'name'        =>    'password',
     *                  'message'    =>    string | null
     *                ],
     *                [
     *                  'name'        =>    'equal',
     *                  'message'    =>    string | null,
     *                    'field'        =>    string
     *                ],
     *                [
     *                  'name'        =>    'match',
     *                  'message'    =>    string | null,
     *                    'pattern'    =>    string
     *                ]
     *        ]
     * ]
     *
     * @return array
     */
    abstract public function rules(): array;

    public function failedValidation() { }
}