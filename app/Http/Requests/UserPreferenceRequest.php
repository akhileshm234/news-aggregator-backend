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
            'preferred_categories' => ['nullable', 'array'],
            'preferred_categories.*' => ['string'],
            'preferred_sources' => ['nullable', 'array'],
            'preferred_sources.*' => ['string'],
            'preferred_authors' => ['nullable', 'array'],
            'preferred_authors.*' => ['string']
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
            'preferred_categories.array' => 'Categories must be an array',
            'preferred_categories.*.string' => 'Each category must be a string',
            'preferred_sources.array' => 'Sources must be an array',
            'preferred_sources.*.string' => 'Each source must be a string',
            'preferred_authors.array' => 'Authors must be an array',
            'preferred_authors.*.string' => 'Each author must be a string'
        ];
    }
} 