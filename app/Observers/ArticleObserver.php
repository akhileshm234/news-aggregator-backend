<?php

namespace App\Observers;

use App\Models\Article;
use App\Events\ArticleProcessed;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    public function created(Article $article)
    {
        Cache::tags(['articles'])->flush();
        event(new ArticleProcessed($article));
    }

    public function updated(Article $article)
    {
        Cache::tags(['articles'])->flush();
    }

    public function deleted(Article $article)
    {
        Cache::tags(['articles'])->flush();
    }
} 