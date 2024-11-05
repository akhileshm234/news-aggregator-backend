<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\News\NewsApiService;
use App\Services\News\GuardianService;
use App\Services\News\NytService;
use App\Repositories\ArticleRepository;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessNewArticles;
use App\Models\Article;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-articles {--source= : Specific source to fetch from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from multiple news sources';

    protected array $services = [];
    protected ArticleRepository $articleRepository;
    protected CacheService $cacheService;

    public function __construct(
        NewsApiService $newsApiService,
        GuardianService $guardianService,
        NytService $nytService,
        ArticleRepository $articleRepository,
        CacheService $cacheService
    ) {
        parent::__construct();
        
        $this->services = [
            'newsapi' => $newsApiService,
            'guardian' => $guardianService,
            'nyt' => $nytService,
        ];
        
        $this->articleRepository = $articleRepository;
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourceOption = $this->option('source');
        $sources = $sourceOption ? [$sourceOption] : array_keys($this->services);

        foreach ($sources as $source) {
            if (!isset($this->services[$source])) {
                $this->error("Invalid source: {$source}");
                continue;
            }

            $this->fetchArticlesFromSource($source);
        }

        // Clear the article cache after fetching new articles
        try {
            $this->cacheService->clear(['articles']);
            $this->info('Cache cleared successfully');
        } catch (\Exception $e) {
            $this->error('Failed to clear cache: ' . $e->getMessage());
            Log::error('Cache clear failed', ['error' => $e->getMessage()]);
        }
    }

    protected function fetchArticlesFromSource(string $source): void
    {
        $this->info("Fetching articles from {$source}...");

        try {
            $service = $this->services[$source];
            $articles = $service->fetch();

            $transformedArticles = array_map(function ($article) use ($source) {
                $transformed = $this->services[$source]->transform($article);
                $transformed['source_id'] = $this->getSourceId($article, $source);
                $transformed['content_hash'] = md5($transformed['title'] . $transformed['content']);
                return $transformed;
            }, $articles);

            foreach ($transformedArticles as $article) {
                try {
                    Article::updateOrCreate(
                        [
                            'source_id' => $article['source_id'],
                            'source' => $article['source']
                        ],
                        $article
                    );
                } catch (\Exception $e) {
                    $this->error("Failed to insert article: {$e->getMessage()}");
                    Log::error('Article insertion failed:', [
                        'error' => $e->getMessage(),
                        'article' => $article
                    ]);
                }
            }

            $this->info("Processed " . count($transformedArticles) . " articles from {$source}");

        } catch (\Exception $e) {
            $this->error("Error processing {$source}: " . $e->getMessage());
        }
    }

    protected function getSourceId($article, string $source): string
    {
        switch ($source) {
            case 'newsapi':
                return $article['source']['id'] ?? $article['id'] ?? uniqid('newsapi_');
                
            case 'guardian':
                return $article['id'] ?? uniqid('guardian_');
                
            case 'nyt':
                return $article['_id'] ?? uniqid('nyt_');
                
            default:
                return uniqid($source . '_');
        }
    }
}
