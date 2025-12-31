<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Gomu\Auth\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gomu\Auth\Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for authentication
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'user_type' => 'internal',
        ]);

        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    public function test_user_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->withToken($this->adminToken)
            ->getJson('/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'username',
                        'email',
                        'user_type',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_user_can_create_user(): void
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'user_type' => 'internal',
        ];

        $response = $this->withToken($this->adminToken)
            ->postJson('/users', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'username',
                    'email',
                    'user_type',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'user_type' => 'internal',
        ]);
    }

    public function test_user_can_show_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withToken($this->adminToken)
            ->getJson("/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                ]
            ]);
    }

    public function test_user_can_update_user(): void
    {
        $user = User::factory()->create([
            'username' => 'oldusername',
            'email' => 'old@example.com',
        ]);

        $updateData = [
            'username' => 'newusername',
            'email' => 'new@example.com',
            'user_type' => 'external',
        ];

        $response = $this->withToken($this->adminToken)
            ->putJson("/users/{$user->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'newusername',
                    'email' => 'new@example.com',
                    'user_type' => 'external',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'newusername',
            'email' => 'new@example.com',
            'user_type' => 'external',
        ]);
    }

    public function test_user_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->withToken($this->adminToken)
            ->deleteJson("/users/{$user->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'User deleted successfully']);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    public function test_user_can_filter_users_by_type(): void
    {
        User::factory()->count(2)->create(['user_type' => 'internal']);
        User::factory()->count(3)->create(['user_type' => 'external']);

        $response = $this->withToken($this->adminToken)
            ->getJson('/users?user_type=internal');

        $response->assertStatus(200);

        // Should only return internal users
        $data = $response->json('data');
        foreach ($data as $user) {
            $this->assertEquals('internal', $user['user_type']);
        }
    }

    public function test_user_can_search_users(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'username' => 'johndoe'
        ]);
        User::factory()->create([
            'email' => 'jane@example.com',
            'username' => 'janesmith'
        ]);
        User::factory()->create([
            'email' => 'bob@example.com',
            'username' => 'bobwilson'
        ]);

        $response = $this->withToken($this->adminToken)
            ->getJson('/users?search=john');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('john@example.com', $data[0]['email']);
    }

    public function test_unauthenticated_user_cannot_access_users(): void
    {
        $response = $this->getJson('/users');

        $response->assertStatus(401);
    }
}