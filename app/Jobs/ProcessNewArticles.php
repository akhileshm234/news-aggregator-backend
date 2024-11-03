<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Facades\Log;

class ProcessNewArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $articles;
    protected string $source;

    public function __construct(array $articles, string $source)
    {
        $this->articles = $articles;
        $this->source = $source;
    }

    public function handle(ArticleRepository $repository)
    {
        $stats = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0
        ];

        foreach ($this->articles as $article) {
            try {
                $result = $repository->createOrUpdate($article, $this->source);
                
                if ($result->wasRecentlyCreated) {
                    $stats['created']++;
                    Log::info("Created new article: {$result->title}");
                } else {
                    $stats['updated']++;
                    Log::info("Updated existing article: {$result->title}");
                }
            } catch (\Exception $e) {
                $stats['failed']++;
                Log::error("Failed to process article", [
                    'source' => $this->source,
                    'article' => $article,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Article processing completed", [
            'source' => $this->source,
            'stats' => $stats
        ]);
    }
} 