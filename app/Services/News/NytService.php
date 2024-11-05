<?php

namespace App\Services\News;

use Carbon\Carbon;

class NytService extends BaseNewsService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://api.nytimes.com/svc/search/v2/';
        $this->apiKey = config('services.nyt.api_key');
    }

    public function fetch(): array
    {
        $params = [
            'api-key' => $this->apiKey,
            'sort' => 'newest',
            'page' => 1
        ];

        $response = $this->get('articlesearch.json', $params);
        return $response['response']['docs'] ?? [];
    }

    public function transform($article): array
    {
        $publishedAt = Carbon::parse($article['pub_date']);
        $imageUrl = null;

        if (!empty($article['multimedia'])) {
            foreach ($article['multimedia'] as $media) {
                if ($media['type'] === 'image') {
                    $imageUrl = 'https://www.nytimes.com/' . $media['url'];
                    break;
                }
            }
        }

        return [
            'title' => $article['headline']['main'] ?? '',
            'content' => $article['abstract'] ?? '',
            'url' => $article['web_url'] ?? '',
            'source' => 'nyt',
            'author' => $article['byline']['original'] ?? null,
            'published_at' => $publishedAt,
            'image_url' => $imageUrl,
            'category' => $article['section_name'] ?? $article['section'] ?? null,
        ];
    }
} 