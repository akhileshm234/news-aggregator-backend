<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleIndexRequest extends FormRequest
{
    public function rules()
    {
        return [
            'keywords' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'category' => 'nullable|string|exists:articles,category',
            'source' => 'nullable|string|exists:articles,source',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
} 