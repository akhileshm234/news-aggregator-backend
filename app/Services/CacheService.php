<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
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