<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Check if the user was created
        $this->assertCount(1, User::all());

        // Check the response
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [  // Corrected to 'data' instead of 'user'
                        'id',
                        'name',
                        'email',
                    ],
                    'message',
                ])
                ->assertJson(['message' => 'User registered successfully']);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // First registration
        $this->postJson('/api/register', $userData);

        // Second registration with same email
        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_register_validates_required_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
