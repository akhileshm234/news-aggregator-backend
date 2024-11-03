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
            // Get the appropriate service
            $service = $this->services[$source];

            // Fetch articles
            $articles = $service->fetch();

            // Transform articles to consistent format
            $transformedArticles = $service->transform($articles);

            // Dispatch job to process articles in the background
            ProcessNewArticles::dispatch($transformedArticles, $source);

            $this->info("Successfully queued " . count($transformedArticles) . " articles from {$source}");

        } catch (\Exception $e) {
            $this->handleError($source, $e);
        }
    }

    protected function handleError(string $source, \Exception $e): void
    {
        $message = "Error fetching from {$source}: " . $e->getMessage();
        
        $this->error($message);
        
        Log::error($message, [
            'source' => $source,
            'exception' => $e,
            'trace' => $e->getTraceAsString()
        ]);
    }
}
