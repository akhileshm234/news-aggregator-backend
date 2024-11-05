<?php

namespace App\Services\News;

use Carbon\Carbon;

class NewsApiService extends BaseNewsService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://newsapi.org/v2/';
        $this->apiKey = config('services.newsapi.api_key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('NewsAPI API key is not configured');
        }
        
        $this->headers['X-Api-Key'] = $this->apiKey;
    }

    public function fetch(): array
    {
        $params = [
            'language' => 'en',
            'pageSize' => 20,
            'sortBy' => 'publishedAt'
        ];

        $response = $this->get('top-headlines', $params);
        return $response['articles'] ?? [];
    }

    public function transform($article): array
    {
        $publishedAt = Carbon::parse($article['publishedAt']);

        return [
            'title' => $article['title'] ?? '',
            'content' => $article['description'] ?? '',
            'url' => $article['url'] ?? '',
            'source' => 'newsapi',
            'author' => $article['author'] ?? null,
            'published_at' => $publishedAt,
            'image_url' => $article['urlToImage'] ?? null,
            'category' => $article['category'] ?? null,
        ];
    }
} 