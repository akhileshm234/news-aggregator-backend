<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|min:3|max:255',
            'source' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort_by' => 'nullable|in:published_at,title',
            'sort_direction' => 'nullable|in:asc,desc'
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.min' => 'Search keyword must be at least 3 characters',
            'date_to.after_or_equal' => 'End date must be after or equal to start date',
        ];
    }
} 