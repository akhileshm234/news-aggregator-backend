<?php

namespace App\Services\News;

use App\Contracts\NewsApiInterface;

class GuardianService extends BaseNewsService implements NewsApiInterface
{
    protected function validateConfig(): void
    {
        $this->apiKey = config('services.guardian.key');
        $this->baseUrl = 'https://content.guardianapis.com/';
        
        if (empty($this->apiKey)) {
            throw new NewsApiException('Guardian API key not configured');
        }
    }

    public function fetch(array $parameters = []): array
    {
        $defaultParams = [
            'show-fields' => 'all',
        ];

        $response = $this->makeRequest('search', array_merge($defaultParams, $parameters));
        return $response['response']['results'] ?? [];
    }

    public function transform(array $articles): array
    {
        return array_map(function ($article) {
            return [
                'source_id' => $article['id'],
                'source' => 'guardian',
                'title' => $article['webTitle'],
                'content' => $article['fields']['bodyText'] ?? '',
                'url' => $article['webUrl'],
                'published_at' => $article['webPublicationDate'],
                'author' => $article['fields']['byline'] ?? null,
                'image_url' => $article['fields']['thumbnail'] ?? null,
            ];
        }, $articles);
    }
} 