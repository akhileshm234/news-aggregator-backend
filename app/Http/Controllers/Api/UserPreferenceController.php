<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="User Preferences",
 *     description="API Endpoints for user preferences"
 * )
 */
class UserPreferenceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Get user preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="keywords", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     )
     * )
     */
    public function show(): JsonResponse
    {
        $preferences = auth()->user()->preferences;
        
        return response()->json([
            'data' => $preferences ?? [
                'categories' => [],
                'sources' => [],
                'keywords' => []
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Update user preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Preferences stored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="preferred_categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="preferred_sources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="preferred_authors", type="array", @OA\Items(type="string"))
     *             ),
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="status_code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="preferred_categories",
     *                     type="array",
     *                     @OA\Items(type="string", example="The preferred categories must be an array.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(UserPreferenceRequest $request): JsonResponse
    {
        $preference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json($preference, Response::HTTP_CREATED);
    }
} 