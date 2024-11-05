<?php

namespace App\Jobs;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNewArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private array $articles,
        private string $source
    ) {}

    public function handle(ArticleRepository $repository)
    {
        try {
            Log::info('Processing articles job started', [
                'source' => $this->source,
                'count' => count($this->articles)
            ]);

            foreach ($this->articles as $article) {
                try {
                    $repository->createOrUpdate($article);
                } catch (\Exception $e) {
                    Log::error('Failed to process article', [
                        'error' => $e->getMessage(),
                        'article' => $article
                    ]);
                }
            }

            Log::info('Processing articles job completed', [
                'source' => $this->source
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing articles', [
                'source' => $this->source,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
} 