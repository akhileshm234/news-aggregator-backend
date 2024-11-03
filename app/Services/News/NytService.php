<?php

namespace App\Services\News;

use App\Contracts\NewsApiInterface;

class NytService extends BaseNewsService implements NewsApiInterface
{
    protected function validateConfig(): void
    {
        $this->apiKey = config('services.nyt.key');
        $this->baseUrl = 'https://api.nytimes.com/svc/';
        
        if (empty($this->apiKey)) {
            throw new NewsApiException('NYT API key not configured');
        }
    }

    public function fetch(array $parameters = []): array
    {
        $response = $this->makeRequest('topstories/v2/home.json', $parameters);
        return $response['results'] ?? [];
    }

    public function transform(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'source_id' => $article['uri'],
                'source' => 'nyt',
                'title' => $article['title'],
                'content' => $article['abstract'],
                'url' => $article['url'],
                'published_at' => $article['published_date'],
                'author' => $article['byline'],
                'image_url' => $article['multimedia'][0]['url'] ?? null,
            ];
        }, $articles);
    }
} 