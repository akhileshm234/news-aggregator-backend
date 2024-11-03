<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ArticleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Article::factory()->count(20)->create();
    }

    public function test_can_get_paginated_articles()
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/articles?per_page=5');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total']
            ]);
    }

    public function test_can_search_articles()
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create([
            'title' => 'Unique Test Title'
        ]);

        $response = $this->getJson('/api/articles?keyword=Unique Test');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', $article->title);
    }
} 