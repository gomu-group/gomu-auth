<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gomu\Auth\Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class PassportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure passport for testing
        Config::set('services.passport', [
            'base_url' => 'https://passport.example.com',
            'client_id' => 'test-client-id',
            'client_secret' => 'test-client-secret',
            'callback_url' => 'https://app.example.com/auth/oauth/callback',
        ]);
    }

    public function test_passport_redirect_creates_valid_redirect(): void
    {
        // Start session for OAuth state storage
        $this->startSession();

        $response = $this->get('/auth/oauth/passport/redirect');

        $response->assertStatus(302);

        $location = $response->headers->get('Location');
        $this->assertStringStartsWith('https://passport.example.com/oauth/authorize?', $location);
        $this->assertStringContainsString('client_id=test-client-id', $location);
        $this->assertStringContainsString('redirect_uri=', $location);
        $this->assertStringContainsString('response_type=code', $location);
    }

    public function test_passport_callback_handles_successful_authentication(): void
    {
        // Mock HTTP responses
        Http::fake([
            'passport.example.com/oauth/token' => Http::response([
                'access_token' => 'oauth-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ]),
            'passport.example.com/api/oauth/user-profile' => Http::response([
                'id' => '123',
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'loginable_id' => null,
                'loginable_type' => null,
            ]),
        ]);

        $response = $this->getJson('/auth/oauth/passport/callback?code=test-auth-code&state=test-state');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'username',
                    'email',
                ]
            ]);

        // Verify user was created
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'user_type' => 'external',
        ]);
    }

    public function test_passport_callback_handles_existing_user(): void
    {
        // Create existing user
        $existingUser = User::factory()->create([
            'email' => 'existing@example.com',
            'user_type' => 'internal',
        ]);

        // Mock HTTP responses
        Http::fake([
            'passport.example.com/oauth/token' => Http::response([
                'access_token' => 'oauth-access-token',
            ]),
            'passport.example.com/api/oauth/user-profile' => Http::response([
                'id' => '456',
                'name' => 'Existing User',
                'email' => 'existing@example.com',
                'loginable_id' => null,
                'loginable_type' => null,
            ]),
        ]);

        $response = $this->getJson('/auth/oauth/passport/callback?code=test-auth-code&state=test-state');

        $response->assertStatus(200);

        // Verify user type was not changed
        $existingUser->refresh();
        $this->assertEquals('internal', $existingUser->user_type);
    }

    public function test_passport_callback_handles_oauth_failure(): void
    {
        // Mock failed OAuth token exchange
        Http::fake([
            'passport.example.com/oauth/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'Authorization code has expired'
            ], 400),
        ]);

        $response = $this->getJson('/auth/oauth/passport/callback?code=expired-code&state=test-state');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Authentication failed'
            ]);
    }

    public function test_passport_callback_requires_code_parameter(): void
    {
        $response = $this->getJson('/auth/oauth/passport/callback');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Authentication failed'
            ]);
    }
}