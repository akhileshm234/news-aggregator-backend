<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_a_user_with_valid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Akhilesh M',
            'email' => 'akhileshm@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', [
            'email' => 'akhileshm@example.com',
        ]);
    }

    /** @test */
    public function it_requires_all_fields_for_registration()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function it_requires_a_unique_email()
    {
        User::factory()->create(['email' => 'akhileshm@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Akhilesh M',
            'email' => 'akhileshm@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_requires_password_confirmation()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Akhilesh M',
            'email' => 'akhileshm@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
