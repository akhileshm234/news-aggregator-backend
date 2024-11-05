<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'source',
        'title',
        'content',
        'url',
        'published_at',
        'author',
        'image_url',
        'category',
        'content_hash'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Add a scope to find duplicates
    public function scopeFindDuplicates($query)
    {
        return $query->select('content_hash')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('content_hash')
            ->having('count', '>', 1);
    }

    // Add a method to get all articles with the same content
    public function getDuplicates()
    {
        return static::where('content_hash', $this->content_hash)
            ->where('id', '!=', $this->id)
            ->get();
    }

    public function getReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutesToRead = ceil($wordCount / 200);
        return max(1, $minutesToRead);
    }
}
