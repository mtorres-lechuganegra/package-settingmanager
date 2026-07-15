<?php

namespace LechugaNegra\SettingManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'group.string' => 'The group field must be a string.',
            'group.max' => 'The group field must not exceed 100 characters.',
        ];
    }
}
