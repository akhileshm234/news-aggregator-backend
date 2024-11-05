<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use Carbon\Carbon;

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour

    public function getCachedArticles(array $params = [])
    {
        $cacheKey = $this->generateCacheKey($params);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($params) {
            return Article::query()
                ->when(isset($params['category']), function ($query) use ($params) {
                    $query->where('category', $params['category']);
                })
                ->when(isset($params['source']), function ($query) use ($params) {
                    $query->where('source', $params['source']);
                })
                ->when(isset($params['date_from']), function ($query) use ($params) {
                    $query->whereDate('published_at', '>=', $params['date_from']);
                })
                ->when(isset($params['date_to']), function ($query) use ($params) {
                    $query->whereDate('published_at', '<=', $params['date_to']);
                })
                ->latest('published_at')
                ->get();
        });
    }

    private function generateCacheKey(array $params): string
    {
        $keyParts = ['articles'];

        if (isset($params['category'])) {
            $keyParts[] = 'category:' . $params['category'];
        }

        if (isset($params['source'])) {
            $keyParts[] = 'source:' . $params['source'];
        }

        if (isset($params['date_from'])) {
            $keyParts[] = 'from:' . $params['date_from'];
        }

        if (isset($params['date_to'])) {
            $keyParts[] = 'to:' . $params['date_to'];
        }

        return implode(':', $keyParts);
    }

    public function clearArticlesCache(): void
    {
        Cache::tags(['articles'])->flush();
    }

    public function remember(string $key, $data, int $minutes = 60)
    {
        return Cache::remember($key, now()->addMinutes($minutes), function () use ($data) {
            return is_callable($data) ? $data() : $data;
        });
    }

    public function clear($tags = null): void
    {
        if (config('cache.default') === 'file') {
            // For file driver, just clear everything
            Cache::flush();
        } else {
            // For drivers that support tags
            if ($tags) {
                if (is_array($tags)) {
                    foreach ($tags as $tag) {
                        Cache::tags($tag)->flush();
                    }
                } else {
                    Cache::tags($tags)->flush();
                }
            } else {
                Cache::flush();
            }
        }
    }

    public function generateArticleKey(array $params): string
    {
        return 'articles:' . md5(serialize($params));
    }

    public function generateUserFeedKey(int $userId, array $params): string
    {
        return "user:{$userId}:feed:" . md5(serialize($params));
    }

    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    public function has(string $key): bool
    {
        return Cache::has($key);
    }
} 