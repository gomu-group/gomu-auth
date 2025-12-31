<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gomu\Auth\Tests\TestCase;

class UserTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_tokens(): void
    {
        $user = User::factory()->create();

        // Create some tokens for the user
        $token1 = $user->createToken('Test Token 1');
        $token2 = $user->createToken('Test Token 2');

        $response = $this->withToken($token1->plainTextToken)
            ->getJson('/auth/user-token');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'tokens' => [
                        '*' => [
                            'id',
                            'name',
                            'abilities',
                            'created_at',
                            'last_used_at',
                            'expires_at',
                        ]
                    ]
                ]
            ]);

        $this->assertCount(2, $response->json('data.tokens'));
    }

    public function test_user_can_revoke_their_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('Test Token');

        // Verify token exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);

        $response = $this->withToken($token->plainTextToken)
            ->deleteJson("/auth/user-token/{$token->accessToken->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token revoked successfully'
            ]);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_user_cannot_revoke_others_token(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $token1 = $user1->createToken('User1 Token');
        $token2 = $user2->createToken('User2 Token');

        $response = $this->withToken($token1->plainTextToken)
            ->deleteJson("/auth/user-token/{$token2->accessToken->id}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Token not found'
            ]);

        // Verify token2 still exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token2->accessToken->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_tokens(): void
    {
        $response = $this->getJson('/auth/user-token');

        $response->assertStatus(401);
    }
}