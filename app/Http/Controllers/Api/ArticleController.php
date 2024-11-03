<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleSearchRequest;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Get(
 *     path="/api/articles",
 *     summary="Get paginated articles",
 *     @OA\Parameter(
 *         name="keyword",
 *         in="query",
 *         description="Search keyword",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation"
 *     )
 * )
 */
class ArticleController extends Controller
{
    protected ArticleRepository $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * List articles with filters and pagination
     */
    public function index(ArticleSearchRequest $request): ResourceCollection
    {
        $articles = $this->repository->search($request->validated());
        
        return ArticleResource::collection($articles)
            ->additional([
                'meta' => [
                    'available_sources' => Article::distinct('source')->pluck('source'),
                    'total_articles' => Article::count(),
                ]
            ]);
    }

    /**
     * Get single article details
     */
    public function show(Article $article): JsonResponse
    {
        return response()->json([
            'data' => new ArticleResource($article),
            'related_articles' => ArticleResource::collection(
                Article::where('source', $article->source)
                    ->where('id', '!=', $article->id)
                    ->latest('published_at')
                    ->take(3)
                    ->get()
            )
        ]);
    }

    /**
     * Search articles
     */
    public function search(ArticleSearchRequest $request): ResourceCollection
    {
        $articles = $this->repository->search($request->validated());
        return ArticleResource::collection($articles);
    }

    public function personalizedFeed(Request $request)
    {
        $articles = $this->repository->getPersonalizedFeed(
            auth()->id(),
            $request->validated()
        );
        return ArticleResource::collection($articles);
    }
} 