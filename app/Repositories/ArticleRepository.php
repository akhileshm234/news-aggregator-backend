<?php

namespace App\Repositories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Events\ArticleProcessed;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ArticleRepository
{
    public function createOrUpdate(array $articleData, string $source): Article
    {
        // Generate a unique hash based on title and content to detect duplicates
        $contentHash = $this->generateContentHash($articleData);

        // First try to find existing article by source_id and source
        $article = Article::where([
            'source_id' => $articleData['source_id'],
            'source' => $source
        ])->first();

        // If not found by source_id, check for duplicates by content hash
        if (!$article) {
            $article = Article::where('content_hash', $contentHash)->first();
        }

        $articleAttributes = [
            'title' => $articleData['title'] ?? '',
            'content' => $articleData['content'] ?? '',
            'url' => $articleData['url'] ?? '',
            'published_at' => $articleData['published_at'] ?? now(),
            'author' => $articleData['author'] ?? null,
            'image_url' => $articleData['image_url'] ?? null,
            'content_hash' => $contentHash,
            'source' => $source,
            'source_id' => $articleData['source_id']
        ];

        if ($article) {
            // Update existing article if content has changed
            if ($article->content_hash !== $contentHash) {
                $article->update($articleAttributes);
                Log::info("Updated duplicate article", [
                    'title' => $article->title,
                    'sources' => [$article->source, $source]
                ]);
            }
        } else {
            // Create new article
            $article = Article::create($articleAttributes);
            Log::info("Created new article", [
                'title' => $article->title,
                'source' => $source
            ]);
        }

        return $article;
    }

    protected function generateContentHash(array $articleData): string
    {
        // Create a normalized version of the content for comparison
        $normalizedContent = Str::lower(
            $articleData['title'] . ' ' . 
            ($articleData['content'] ?? '')
        );

        // Remove common variations and special characters
        $normalizedContent = preg_replace('/\s+/', ' ', $normalizedContent);
        $normalizedContent = preg_replace('/[^\w\s]/', '', $normalizedContent);

        return md5($normalizedContent);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Article::query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['keyword']}%")
                  ->orWhere('content', 'like', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['source'])) {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', $filters['date_to']);
        }

        return $query->latest('published_at')
                    ->paginate($filters['per_page'] ?? 15);
    }

    public function getPersonalizedFeed(int $userId, array $params = []): LengthAwarePaginator
    {
        $preferences = UserPreference::where('user_id', $userId)->first();
        
        $query = Article::query();

        if ($preferences?->preferred_sources) {
            $query->whereIn('source', $preferences->preferred_sources);
        }

        if ($preferences?->preferred_categories) {
            $query->whereIn('category', $preferences->preferred_categories);
        }

        return $query->latest('published_at')
                    ->paginate($params['per_page'] ?? 15);
    }
} 