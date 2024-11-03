<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'summary' => $this->getSummary(),
            'url' => $this->url,
            'source' => $this->source,
            'additional_sources' => $this->additional_sources,
            'author' => $this->author,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'reading_time' => $this->getReadingTime(),
        ];
    }
} 