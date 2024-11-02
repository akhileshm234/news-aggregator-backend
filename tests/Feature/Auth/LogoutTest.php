<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a token for the user
        $token = $user->createToken('test-token')->plainTextToken;

        // Attempt to logout with token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out'
            ]);

        // Assert token was deleted
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        // Attempt to logout without token
        $response = $this->postJson('/api/logout');

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_user_with_invalid_token_cannot_logout(): void
    {
        // Attempt to logout with invalid token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->postJson('/api/logout');

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }
} 