<?php

namespace App\Virtual\Models;

/**
 * @OA\Schema(
 *     title="Article",
 *     description="Article model",
 *     @OA\Xml(name="Article")
 * )
 */
class Article
{
    /**
     * @OA\Property(type="integer", example=1)
     */
    private $id;

    /**
     * @OA\Property(type="string", example="Article Title")
     */
    private $title;

    /**
     * @OA\Property(type="string", example="Article content...")
     */
    private $content;

    /**
     * @OA\Property(type="string", example="A brief summary of the article...")
     */
    private $summary;

    /**
     * @OA\Property(type="string", example="https://example.com/article")
     */
    private $url;

    /**
     * @OA\Property(type="string", example="news_source")
     */
    private $source;

    /**
     * @OA\Property(type="string", example="technology")
     */
    private $category;

    /**
     * @OA\Property(type="string", example="John Doe")
     */
    private $author;

    /**
     * @OA\Property(type="string", example="https://example.com/image.jpg")
     */
    private $image_url;

    /**
     * @OA\Property(type="string", format="date-time")
     */
    private $published_at;

    /**
     * @OA\Property(type="string", format="date-time")
     */
    private $created_at;

    /**
     * @OA\Property(type="integer", example=5)
     */
    private $reading_time;
} 