<?php


namespace RingleSoft\JasminClient\Validators;

use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\Rules\RequiredIf;

class RestBatchValidator
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return  [
            'batch_config' => 'nullable|array',
            'batch_config.callback_url' => 'nullable|url',
            'batch_config.errback_url' => 'nullable|url',
            'globals' => 'nullable|array',
            'globals.from' => 'nullable|string',
            'globals.dlr-level' => 'required|integer',
            'globals.dlr' => 'required|in:yes,no',
            'globals.dlr-url' => 'required|url',
            'messages.*.from' => [
                'nullable',
                new RequiredIf(function ($input, $value) {
                    return is_null(data_get($input, 'globals.from'));
                }),
                'string',
            ],
            'messages.*.to' => 'required|array',
            'messages.*.to.*' => 'required|numeric',
            'messages.*.content' => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return  [
            'globals.from.required' => 'The from field is required.',
            'globals.dlr-level.required' => 'The dlr-level field is required.',
            'globals.dlr.required' => 'The dlr field is required.',
            'globals.dlr-url.required' => 'The dlr-url field is required.',
            'globals.dlr-url.url' => 'The dlr-url must be a valid URL.',
            'messages.*.from.required' => 'The from field is required if globals.from is not set.',
            'messages.*.from.string' => 'The from field must be a string.',
            'messages.*.to.required' => 'The to field is required.',
            'messages.*.to.*.required' => 'The to field must contain at least one number.',
            'messages.*.to.*.numeric' => 'The to field must contain only numbers.',
            'messages.*.content.required' => 'The content field is required.',
            'messages.*.content.string' => 'The content field must be a string.',
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
