<?php

namespace App\Observers;

use App\Models\Article;
use App\Events\NewArticlePublished;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    public function created(Article $article)
    {
        NewArticlePublished::dispatch($article);
        Cache::tags(['articles'])->flush();
    }

    public function updated(Article $article)
    {
        Cache::tags(['articles'])->flush();
    }
} 