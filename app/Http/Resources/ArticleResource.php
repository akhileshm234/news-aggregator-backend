<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ArticleResource",
 *     title="Article Resource",
 *     description="Article resource",
 *     @OA\Property(property="id", type="integer", example=34),
 *     @OA\Property(property="title", type="string", example="How One Lebanese Town Is Trying to Stay Out of the War Around It"),
 *     @OA\Property(property="content", type="string", example="While Lebanese are united against Israel's onslaught..."),
 *     @OA\Property(property="summary", type="string", example="While Lebanese are united against Israel's onslaught..."),
 *     @OA\Property(property="url", type="string", example="https://www.nytimes.com/2024/11/03/world/middleeast/lebanon-hezbollah-israel.html"),
 *     @OA\Property(property="source", type="string", example="nyt"),
 *     @OA\Property(property="additional_sources", type="string", nullable=true),
 *     @OA\Property(property="author", type="string", example="By Christina Goldbaum"),
 *     @OA\Property(property="image_url", type="string", example="https://static01.nyt.com/images/2024/11/03/multimedia/03lebanon-hasbayya-promo/03lebanon-hasbayya-01-mpzw-superJumbo.jpg"),
 *     @OA\Property(property="published_at", type="string", format="datetime", example="2024-11-03 00:01:25"),
 *     @OA\Property(property="created_at", type="string", format="datetime", example="2024-11-03 07:15:38"),
 *     @OA\Property(property="reading_time", type="integer", example=1)
 * )
 */
class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'url' => $this->url,
            'source' => $this->source,
            'category' => $this->category,
            'additional_sources' => $this->additional_sources,
            'author' => $this->author,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'reading_time' => $this->getReadingTime(),
        ];
    }
} 