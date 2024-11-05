<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_specific_article()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        
        // Ensure the user has a token
        $token = $user->createToken('Test Token')->plainTextToken;

        // Create an article
        $article = Article::factory()->create([
            'title' => 'Test Article',
            'content' => 'This is a test article content.',
        ]);

        // Authenticate with Sanctum token and retrieve the article
        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson("/api/articles/{$article->id}");

        // Assert the response is 200 OK
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Test Article'])
                 ->assertJsonFragment(['content' => 'This is a test article content.']);
    }

    /** @test */
    public function cannot_get_non_existent_article()
    {
        // Create a user and authenticate
        $user = User::factory()->create();
        
        // Ensure the user has a token
        $token = $user->createToken('Test Token')->plainTextToken;

        // Attempt to get a non-existent article (ID 9999)
        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->getJson('/api/articles/9999');

        // Assert the response status is 404 Not Found
        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Resource not found.', // Notice the period at the end
                    'status_code' => 404,
                ]);
    }
}
