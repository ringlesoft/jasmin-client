<?php

namespace RingleSoft\JasminClient\Validators;

use Illuminate\Support\Facades\Validator;

class HttpMessageValidator
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'to' => 'required|int',
            'from' => 'nullable|string|max:20',
            'coding' => 'nullable|integer|in:0,1,2,3,4,5,6,7,8,9,10,13,14',
            'username' => 'required|string|max:30',
            'password' => 'required|string|max:30',
            'priority' => 'nullable|integer|in:0,1,2,3',
            'sdt' => 'nullable|string',
            'validity-period' => 'nullable|integer',
            'dlr' => 'nullable|in:yes,no',
            'dlr-url' => 'required_if:dlr,yes|url',
            'dlr-level' => 'required_if:dlr,yes|integer|in:1,2,3',
            'dlr-method' => 'required_if:dlr,yes|in:GET,POST',
            'tags' => 'nullable|string',
            'content' => 'required_without:hex-content|string|nullable',
//            'hex-content' => 'required_without:content|string|nullable|regex:/^[0-9A-Fa-f]+$/',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'to.required' => 'The destination address is required.',
            'to.max' => 'The destination address must not exceed 20 characters.',
            'from.max' => 'The originating address must not exceed 20 characters.',
            'username.required' => 'The username for Jasmin user account is required.',
            'username.max' => 'The username must not exceed 30 characters.',
            'password.required' => 'The password for Jasmin user account is required.',
            'password.max' => 'The password must not exceed 30 characters.',
            'priority.in' => 'The priority must be 0, 1, 2 or 3.',
            'dlr.in' => 'The dlr value must be either "yes" or "no".',
            'dlr-url.required_if' => 'The dlr-url is required when dlr is set to "yes".',
            'dlr-level.required_if' => 'The dlr-level is required when dlr is set to "yes".',
            'dlr-level.in' => 'The dlr-level must be 1, 2 or 3.',
            'dlr-method.required_if' => 'The dlr-method is required when dlr is set to "yes".',
            'dlr-method.in' => 'The dlr-method must be either GET or POST.',
            'content.required_without' => 'The content field is required when hex-content is not present.',
            'hex-content.required_without' => 'The hex-content field is required when content is not present.',
            'hex-content.regex' => 'The hex-content must be a valid hexadecimal string.',
        ];
    }

    private function validateData($data): \Illuminate\Validation\Validator
    {
        return Validator::make($data, $this->rules(), $this->messages());
    }

    public static function validate(array $data): \Illuminate\Validation\Validator
    {
        return (new static())->validateData($data);
    }
}
