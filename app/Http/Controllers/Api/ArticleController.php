<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Requests\ArticleSearchRequest;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleIndexRequest;

/**
 * Class ArticleController
 * @package App\Http\Controllers\Api
 */
class ArticleController extends Controller
{
    protected ArticleRepository $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get paginated list of articles.
     *
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get paginated articles list with filters",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Application/json",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Parameter(
     *         name="keywords",
     *         in="query",
     *         description="Search keywords in title and content",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter articles from date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter articles to date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by source",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ArticleResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "per_page": {"The per page must be between 1 and 100"},
     *                     "date_from": {"The date from must be a valid date"}
     *                 }
     *             )
     *         )
     *     )
     * )
     */
    public function index(ArticleIndexRequest $request)
    {
        $perPage = $request->input('per_page', 15);
        $keywords = $request->input('keywords');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $category = $request->input('category');
        $source = $request->input('source');
        
        $query = Article::query();
        
        // Keyword search in title and content
        if ($keywords) {
            $query->where(function($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                  ->orWhere('content', 'like', "%{$keywords}%");
            });
        }
        
        // Date range filter
        if ($dateFrom) {
            $query->whereDate('published_at', '>=', $dateFrom);
        }
        
        if ($dateTo) {
            $query->whereDate('published_at', '<=', $dateTo);
        }
        
        // Category filter
        if ($category) {
            $query->where('category', $category);
        }
        
        // Source filter
        if ($source) {
            $query->where('source', $source);
        }
        
        $articles = $query->latest('published_at')->paginate($perPage);
        
        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'last_page' => $articles->lastPage()
            ]
        ]);
    }

    /**
     * Get single article details
     * 
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get single article details",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Application/json",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Article ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/ArticleResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Article not found"),
     *                 @OA\Property(property="status_code", type="integer", example=404)
     *             )
     *         )
     *     )
     * )
     */
    public function show(Article $article): JsonResponse
    {
        return response()->json([
            'data' => new ArticleResource($article)
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
        echo "jjjj";
        $articles = $this->repository->getPersonalizedFeed(
            auth()->id(),
            $request->validated()
        );
        return ArticleResource::collection($articles);
    }

    /**
     * Get personalized news feed based on user preferences
     * 
     * @OA\Get(
     *     path="/api/articles/personalized",
     *     tags={"Articles"},
     *     summary="Get personalized news feed",
     *     description="Fetch articles based on user's preferred categories, sources, and authors",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Application/json",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/ArticleResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="last_page", type="integer", example=4)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No articles found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="No articles found"),
     *                 @OA\Property(property="status_code", type="integer", example=404)
     *             )
     *         )
     *     )
     * )
     */
    public function personalized(Request $request)
    {
        $user = auth()->user();
        $preferences = $user->preferences;
        $perPage = $request->input('per_page', 15);

        // Add debug logging
        \Log::info('User ID: ' . $user->id);
        \Log::info('Preferences: ', (array) $preferences);

        $query = Article::query();

        if ($preferences) {
            $query->where(function ($q) use ($preferences) {
                // Filter by preferred categories
                if (!empty($preferences->preferred_categories)) {
                    $q->whereIn('category', $preferences->preferred_categories);
                }

                // Filter by preferred sources
                if (!empty($preferences->preferred_sources)) {
                    $q->orWhereIn('source', $preferences->preferred_sources);
                }

                // Filter by preferred authors
                if (!empty($preferences->preferred_authors)) {
                    $q->orWhereIn('author', $preferences->preferred_authors);
                }
            });
        }

        // Order by published date
        $query->latest('published_at');

        // Debug the query
        \Log::info('SQL Query: ' . $query->toSql());
        \Log::info('Query Bindings: ', $query->getBindings());

        $articles = $query->paginate($perPage);

        if ($articles->isEmpty()) {
            return response()->json([
                'error' => [
                    'message' => 'No articles found',
                    'status_code' => 404
                ]
            ], 404);
        }

        return response()->json([
            'data' => ArticleResource::collection($articles),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'total' => $articles->total(),
                'per_page' => $articles->perPage(),
                'last_page' => $articles->lastPage()
            ]
        ]);
    }
} 