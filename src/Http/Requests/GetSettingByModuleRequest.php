<?php

namespace LechugaNegra\SettingManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetSettingByModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'only_active' => ['sometimes', 'nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'only_active.boolean' => 'The only_active field must be a boolean.',
        ];
    }
}
