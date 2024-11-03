<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserPreferenceRequest;
use App\Models\UserPreference;
use Illuminate\Http\Response;

class UserPreferenceController extends Controller
{
    /**
     * Get user preferences
     * 
     * @OA\Get(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Get user preferences",
     *     description="Retrieve the authenticated user's preferences for categories, sources, and authors",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Application/json",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User preferences retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="preferred_categories",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="technology")
     *             ),
     *             @OA\Property(
     *                 property="preferred_sources",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="nyt")
     *             ),
     *             @OA\Property(
     *                 property="preferred_authors",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="John Doe")
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="datetime",
     *                 example="2024-03-20T12:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="datetime",
     *                 example="2024-03-20T12:00:00Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Unauthenticated"),
     *                 @OA\Property(property="status_code", type="integer", example=401)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No preferences found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="No preferences found"),
     *                 @OA\Property(property="status_code", type="integer", example=404)
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $preferences = auth()->user()->preferences;
        
        if (!$preferences) {
            return response()->json([
                'error' => [
                    'message' => 'No preferences found',
                    'status_code' => 404
                ]
            ], 404);
        }

        return response()->json($preferences);
    }


     /**
     * Store or update user preferences
     * 
     * @OA\Post(
     *     path="/api/preferences",
     *     tags={"User Preferences"},
     *     summary="Store or update user preferences",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Application/json",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User preference data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="preferred_categories",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="technology"),
     *                 description="Array of preferred categories"
     *             ),
     *             @OA\Property(
     *                 property="preferred_sources",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="nyt"),
     *                 description="Array of preferred news sources"
     *             ),
     *             @OA\Property(
     *                 property="preferred_authors",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="John Doe"),
     *                 description="Array of preferred authors"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Preferences stored successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="preferred_categories",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="technology")
     *             ),
     *             @OA\Property(
     *                 property="preferred_sources",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="nyt")
     *             ),
     *             @OA\Property(
     *                 property="preferred_authors",
     *                 type="array",
     *                 nullable=true,
     *                 @OA\Items(type="string", example="John Doe")
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 format="datetime",
     *                 example="2024-03-20T12:00:00Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 format="datetime",
     *                 example="2024-03-20T12:00:00Z"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors"
     *     )
     * )
     */
    public function store(UserPreferenceRequest $request)
    {
        $preference = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->validated()
        );

        return response()->json($preference, Response::HTTP_CREATED);
    }
} 