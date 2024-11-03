<?php

namespace App\Services\News;

use App\Contracts\NewsApiInterface;
use App\Exceptions\NewsApiException;

class NewsApiService extends BaseNewsService implements NewsApiInterface
{
    protected function validateConfig(): void
    {
        $this->apiKey = config('services.newsapi.key');
        $this->baseUrl = 'https://newsapi.org/v2/';
        
        if (empty($this->apiKey)) {
            throw new NewsApiException('NewsAPI key not configured');
        }
    }

    public function fetch(array $parameters = []): array
    {
        $defaultParams = [
            'language' => 'en',
            'apiKey' => $this->apiKey
        ];

        $response = $this->makeRequest('top-headlines', array_merge($defaultParams, $parameters));
        return $response['articles'] ?? [];
    }

    public function transform(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'source_id' => $article['source']['id'] ?? uniqid(),
                'source' => 'newsapi',
                'title' => $article['title'],
                'content' => $article['content'],
                'url' => $article['url'],
                'published_at' => $article['publishedAt'],
                'author' => $article['author'] ?? null,
                'image_url' => $article['urlToImage'] ?? null,
            ];
        }, $articles);
    }
} 