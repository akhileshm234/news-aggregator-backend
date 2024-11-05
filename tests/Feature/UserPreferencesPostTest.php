<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserPreferencesPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user successfully updates preferences.
     *
     * @return void
     */
    public function test_authenticated_user_updates_preferences()
    {
        // Create a user
        $user = User::factory()->create();

        // Define valid preferences data
        $data = [
            'preferred_categories' => ['category1', 'category2'],
            'preferred_sources' => ['source1', 'source2'],
            'preferred_authors' => ['author1', 'author2'],
        ];

        // Act as the created user and send the POST request
        $response = $this->actingAs($user)->postJson('/api/preferences', $data);

        // Assert the response is successful and preferences are updated
        $response->assertStatus(Response::HTTP_CREATED)
                ->assertJson([
                    'preferred_categories' => ['category1', 'category2'],
                    'preferred_sources' => ['source1', 'source2'],
                    'preferred_authors' => ['author1', 'author2'],
                ]);
    }

    /**
     * Test unauthenticated user attempts to update preferences.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_update_preferences()
    {
        // Define valid preferences data
        $data = [
            'preferred_categories' => ['category1', 'category2'],
            'preferred_sources' => ['source1', 'source2'],
            'preferred_authors' => ['author1', 'author2'],
        ];

        // Send the POST request without authentication
        $response = $this->postJson('/api/preferences', $data);

        // Assert the response is Unauthorized (401)
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
                 ->assertJson([
                     'message' => 'Unauthenticated.',
                     'status_code' => 401,
                 ]);
    }

    /**
     * Test validation error when required fields are missing or invalid.
     *
     * @return void
     */
    public function test_validation_error_for_invalid_preferences()
    {
        // Create a user
        $user = User::factory()->create();

        // Define invalid preferences data (e.g., preferred_categories is not an array)
        $data = [
            'preferred_categories' => 'category1', // Invalid: should be an array
            'preferred_sources' => ['source1', 'source2'],
            'preferred_authors' => ['author1', 'author2'],
        ];

        // Act as the created user and send the POST request with invalid data
        $response = $this->actingAs($user)->postJson('/api/preferences', $data);

        // Assert the response has validation errors (422)
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJson([
                    'message' => 'The given data was invalid.',
                    'errors' => [
                        'preferred_categories' => [
                            'Preferred categories must be an array',
                        ]
                    ]
                ]);
    }

}
