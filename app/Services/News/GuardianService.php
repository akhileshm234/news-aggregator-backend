<?php

namespace App\Services\News;

use Carbon\Carbon;

class GuardianService extends BaseNewsService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://content.guardianapis.com/';
        $this->apiKey = config('services.guardian.api_key');
    }

    public function fetch(): array
    {
        $params = [
            'api-key' => $this->apiKey,
            'show-fields' => 'all',
            'page-size' => 10,
            'order-by' => 'newest'
        ];

        $response = $this->get('search', $params);
        return $response['response']['results'] ?? [];
    }

    public function transform($article): array
    {
        $publishedAt = Carbon::parse($article['webPublicationDate']);

        return [
            'title' => $article['webTitle'] ?? '',
            'content' => $article['fields']['bodyText'] ?? '',
            'url' => $article['webUrl'] ?? '',
            'source' => 'guardian',
            'author' => $article['fields']['byline'] ?? null,
            'published_at' => $publishedAt,
            'image_url' => $article['fields']['thumbnail'] ?? null,
            'category' => $article['sectionName'] ?? $article['section'] ?? null,
        ];
    }
} 