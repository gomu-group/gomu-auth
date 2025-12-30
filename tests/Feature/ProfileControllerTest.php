<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Gomu\Auth\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gomu\Auth\Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_profile(): void
    {
        // Create a user
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'user_type' => 'internal',
        ]);

        // Create an employee linked to the user
        Employee::factory()->create([
            'user_id' => $user->id,
            'nip' => '123456789',
        ]);

        // Act as the user and make a request
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/user-information');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'username' => 'testuser',
                    'email' => 'test@example.com',
                    'role' => null, // Since role is removed
                    'employee' => [
                        'nip' => '123456789',
                    ],
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/user-information');

        $response->assertStatus(401);
    }
}