<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleRepository
{
    public function createOrUpdate(array $data)
    {
        try {
            return Article::updateOrCreate(
                [
                    'source_id' => $data['source_id'],
                    'source' => $data['source']
                ],
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'url' => $data['url'],
                    'author' => $data['author'],
                    'published_at' => $data['published_at'],
                    'image_url' => $data['image_url'],
                    'category' => $data['category'],
                    'content_hash' => $data['content_hash']
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error creating/updating article', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }
} 