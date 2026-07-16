<?php

namespace LechugaNegra\SettingManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingByModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => 'required|array|min:1',
            'data.*.group' => 'nullable|string',
            'data.*.key' => 'required|string',
            'data.*.value' => 'required',
            'data.*.is_active' => 'sometimes|nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'The data field is required.',
            'data.array' => 'The data field must be an array.',
            'data.min' => 'At least one setting must be provided.',
            'data.*.group.string' => 'Each setting group must be a string.',
            'data.*.key.required' => 'Each setting must have a key.',
            'data.*.key.string' => 'Each setting key must be a string.',
            'data.*.value.required' => 'Each setting must have a value.',
            'data.*.is_active.boolean' => 'Each setting is_active must be a boolean.',
        ];
    }
}
