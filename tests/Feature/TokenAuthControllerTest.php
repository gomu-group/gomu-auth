<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Gomu\Auth\Tests\TestCase;

class TokenAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token(): void
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password'),
            'user_type' => 'internal',
        ]);

        // Make login request
        $response = $this->postJson('/auth/token', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                ],
            ]);
    }

    public function test_invalid_credentials_fail_login(): void
    {
        $response = $this->postJson('/auth/token', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_internal_user_can_login_via_internal_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'internal@example.com',
            'password_hash' => Hash::make('password'),
            'user_type' => 'internal',
        ]);

        $response = $this->postJson('/auth/internal/token', [
            'email' => 'internal@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                ],
            ]);
    }

    public function test_external_user_cannot_login_via_internal_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'external@example.com',
            'password_hash' => Hash::make('password'),
            'user_type' => 'external',
        ]);

        $response = $this->postJson('/auth/internal/token', [
            'email' => 'external@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_external_user_can_login_via_external_endpoint(): void
    {
        $user = User::factory()->create([
            'email' => 'external@example.com',
            'password_hash' => Hash::make('password'),
            'user_type' => 'external',
        ]);

        $response = $this->postJson('/auth/external/token', [
            'email' => 'external@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'access_token',
                ],
            ]);
    }

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/auth/register', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'user_type' => 'internal',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'message' => 'User registered successfully',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'user_type' => 'internal',
        ]);
    }

    public function test_internal_user_can_register_via_internal_endpoint(): void
    {
        $response = $this->postJson('/auth/internal/register', [
            'username' => 'internaluser',
            'email' => 'internaluser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'message' => 'Internal user registered successfully',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'internaluser',
            'email' => 'internaluser@example.com',
            'user_type' => 'internal',
        ]);
    }

    public function test_external_user_can_register_via_external_endpoint(): void
    {
        $response = $this->postJson('/auth/external/register', [
            'username' => 'externaluser',
            'email' => 'externaluser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'message' => 'External user registered successfully',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'externaluser',
            'email' => 'externaluser@example.com',
            'user_type' => 'external',
        ]);
    }

    public function test_registration_validation_fails(): void
    {
        $response = $this->postJson('/auth/register', [
            'email' => 'invalid-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password', 'user_type']);
    }
}