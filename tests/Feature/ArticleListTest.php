<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_paginated_articles()
    {
        // Create a user and get an API token
        $user = \App\Models\User::factory()->create();  // Create a user
        $token = $user->createToken('Test Token')->plainTextToken;  // Generate a token

        // Create some articles (make sure they are published and accessible)
        \App\Models\Article::factory()->count(30)->create();

        // Make the request with the authentication token
        $response = $this->getJson('/api/articles?per_page=10&page=1', [
            'Authorization' => 'Bearer ' . $token,  // Pass the token as Authorization header
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'published_at', 
                            // Add other fields as necessary
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'total',
                        'per_page',
                        'last_page',
                    ]
                ]);
    }


    /** @test */
    public function can_search_articles()
    {
        // Create a user and get an API token
        $user = \App\Models\User::factory()->create();  // Create a user
        $token = $user->createToken('Test Token')->plainTextToken;  // Generate a token

        // Create articles with specific titles
        \App\Models\Article::factory()->create(['title' => 'Laravel Testing']);
        \App\Models\Article::factory()->create(['title' => 'PHP Unit Testing']);
        \App\Models\Article::factory()->create(['title' => 'Feature Testing']);

        // Make the request with the authentication token
        $response = $this->getJson('/api/articles?keywords=Laravel', [
            'Authorization' => 'Bearer ' . $token,  // Pass the token as Authorization header
        ]);

        // Assert the response is OK and contains the expected results
        $response->assertStatus(200)
                ->assertJsonCount(1, 'data')  // Expecting one article
                ->assertJsonFragment(['title' => 'Laravel Testing']);
    }
}