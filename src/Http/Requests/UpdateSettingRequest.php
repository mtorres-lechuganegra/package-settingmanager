<?php

namespace LechugaNegra\SettingManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => 'nullable|string',
            'value' => 'required',
            'is_active' => 'sometimes|nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'group.string' => 'The setting group must be a string.',
            'value.required' => 'The setting must have a value.',
            'is_active.boolean' => 'The is_active field must be a boolean.',
        ];
    }
}
