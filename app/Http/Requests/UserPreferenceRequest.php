<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserPreferenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'string|max:50',
            'preferred_sources' => 'nullable|array',
            'preferred_sources.*' => 'string|max:50',
            'preferred_authors' => 'nullable|array',
            'preferred_authors.*' => 'string|max:50',
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
            'preferred_categories.array' => 'Preferred categories must be an array',
            'preferred_categories.*.string' => 'Each preferred category must be a string',
            'preferred_sources.array' => 'Preferred sources must be an array',
            'preferred_sources.*.string' => 'Each preferred source must be a string',
            'preferred_authors.array' => 'Preferred authors must be an array',
            'preferred_authors.*.string' => 'Each preferred author must be a string',
        ];
    }
} 