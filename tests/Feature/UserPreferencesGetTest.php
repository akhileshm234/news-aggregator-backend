<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferencesGetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test showing preferences for authenticated user without preferences.
     *
     * @return void
     */
    public function test_show_preferences_for_authenticated_user_without_preferences()
    {
        // Create a user
        $user = User::factory()->create();

        // Act as the created user (no preferences set)
        $response = $this->actingAs($user)->getJson('/api/preferences');

        // Assert the response
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'categories' => [],
                         'sources' => [],
                         'keywords' => []
                     ]
                 ]);
    }

    /**
     * Test showing preferences for unauthenticated user.
     *
     * @return void
     */
    public function test_show_preferences_for_unauthenticated_user()
    {
        // Send request without authentication
        $response = $this->getJson('/api/preferences');

        // Assert the response is 401 Unauthorized
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated.',  // Include the period here
                     'status_code' => 401
                 ]);
    }
}
