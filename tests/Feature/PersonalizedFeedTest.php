<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalizedFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_personalized_feed()
    {
        // Step 1: Create a user and get an API token
        $user = User::factory()->create(); // Create a user
        $token = $user->createToken('Test Token')->plainTextToken; // Generate a token

        // Step 2: Create user preferences (category, source, and author preferences)
        $user->preferences()->create([
            'preferred_categories' => ['Technology', 'Health'],
            'preferred_sources' => ['Source A', 'Source B'],
            'preferred_authors' => ['Author X'],
        ]);

        // Step 3: Create articles that match the user's preferences
        Article::factory()->create([
            'category' => 'Technology',
            'source' => 'Source A',
            'author' => 'Author X',
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'category' => 'Health',
            'source' => 'Source B',
            'author' => 'Author Y',  // This one doesn't match user preferences for authors.
            'published_at' => now(),
        ]);

        Article::factory()->create([
            'category' => 'Sports',
            'source' => 'Source A',
            'author' => 'Author Z',  // This one doesn't match user preferences for category or author.
            'published_at' => now(),
        ]);

        // Step 4: Make the request with the Bearer token
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->getJson('/api/personalized-feed');

        // Step 5: Assert the response status is 200
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'content',
                             'category',
                             'source',
                             'author',
                             'published_at',
                         ]
                     ],
                     'meta' => [
                         'current_page',
                         'total',
                         'per_page',
                         'last_page',
                     ]
                 ]);

        // Step 6: Assert that only personalized articles are returned (matching preferences)
        // We now expect 1 article that matches the preferences (Technology category, Source A, Author X).
        $response->assertJsonCount(1, 'data'); // Should return 1 article that matches preferences
    }

    /** @test */
    public function unauthenticated_user_cannot_get_personalized_feed()
    {
        // Step 1: Create articles (without creating a user, as the user is unauthenticated)
        Article::factory()->create(['category' => 'Technology', 'source' => 'Source A', 'author' => 'Author X']);
        Article::factory()->create(['category' => 'Health', 'source' => 'Source B', 'author' => 'Author Y']);

        // Step 2: Make the request without authentication
        $response = $this->getJson('/api/personalized-feed');

        // Step 3: Assert the response status is 401 (Unauthenticated)
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.',
                     'status_code' => 401,
                 ]);
    }
}
