<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Foundation\Events\Dispatchable;

class ArticleProcessed
{
    use Dispatchable;

    public function __construct(
        public Article $article,
        public bool $wasCreated,
        public string $source
    ) {}
} 