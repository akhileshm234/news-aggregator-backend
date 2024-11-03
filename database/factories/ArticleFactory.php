<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'source_id' => $this->faker->uuid,
            'source' => $this->faker->randomElement(['newsapi', 'guardian', 'nyt']),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'url' => $this->faker->url,
            'published_at' => $this->faker->dateTimeThisMonth,
            'author' => $this->faker->name,
            'image_url' => $this->faker->imageUrl,
        ];
    }
} 