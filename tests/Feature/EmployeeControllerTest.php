<?php

declare(strict_types=1);

namespace Gomu\Auth\Tests\Feature;

use Gomu\Auth\Models\User;
use Gomu\Auth\Models\Employee;
use Gomu\Auth\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gomu\Auth\Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $adminToken;
    private Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for authentication
        $this->adminUser = User::factory()->create([
            'email' => 'admin@example.com',
            'user_type' => 'internal',
        ]);

        $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;

        // Create a department for testing
        $this->department = Department::create([
            'id' => fake()->uuid(),
            'name' => 'IT Department',
            'code' => 'IT',
        ]);
    }

    public function test_user_can_list_employees(): void
    {
        Employee::factory()->count(3)->create();

        $response = $this->withToken($this->adminToken)
            ->getJson('/employees');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user',
                        'nip',
                        'full_name',
                        'department',
                    ]
                ],
                'links',
                'meta'
            ]);
    }

    public function test_user_can_create_employee(): void
    {
        $user = User::factory()->create(['user_type' => 'internal']);

        $employeeData = [
            'user_id' => $user->id,
            'nip' => '123456789',
            'full_name' => 'John Doe',
            'join_date' => '2023-01-01',
            'department_id' => $this->department->id,
        ];

        $response = $this->withToken($this->adminToken)
            ->postJson('/employees', $employeeData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user',
                    'nip',
                    'full_name',
                    'department',
                ]
            ]);

        $this->assertDatabaseHas('employees', [
            'user_id' => $user->id,
            'nip' => '123456789',
            'full_name' => 'John Doe',
        ]);
    }

    public function test_user_can_show_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->withToken($this->adminToken)
            ->getJson("/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $employee->id,
                    'nip' => $employee->nip,
                    'full_name' => $employee->full_name,
                ]
            ]);
    }

    public function test_user_can_update_employee(): void
    {
        $employee = Employee::factory()->create([
            'full_name' => 'Old Name',
        ]);

        $updateData = [
            'full_name' => 'New Name',
            'phone_number' => '08123456789',
        ];

        $response = $this->withToken($this->adminToken)
            ->putJson("/employees/{$employee->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'full_name' => 'New Name',
                    'phone_number' => '08123456789',
                ]
            ]);

        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'full_name' => 'New Name',
            'phone_number' => '08123456789',
        ]);
    }

    public function test_user_can_delete_employee(): void
    {
        $employee = Employee::factory()->create();

        $response = $this->withToken($this->adminToken)
            ->deleteJson("/employees/{$employee->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Employee deleted successfully']);

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_user_can_filter_employees_by_department(): void
    {
        $dept1 = Department::create([
            'id' => fake()->uuid(),
            'name' => 'HR Department',
            'code' => 'HR',
        ]);

        Employee::factory()->count(2)->create(['department_id' => $this->department->id]);
        Employee::factory()->count(3)->create(['department_id' => $dept1->id]);

        $response = $this->withToken($this->adminToken)
            ->getJson('/employees?department_id=' . $this->department->id);

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data);
        foreach ($data as $employee) {
            $this->assertEquals($this->department->id, $employee['department']['id']);
        }
    }

    public function test_user_can_search_employees(): void
    {
        Employee::factory()->create(['full_name' => 'John Smith']);
        Employee::factory()->create(['full_name' => 'Jane Doe']);
        Employee::factory()->create(['full_name' => 'Bob Johnson']);

        $response = $this->withToken($this->adminToken)
            ->getJson('/employees?search=john');

        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertCount(2, $data); // John Smith and Bob Johnson
    }

    public function test_unauthenticated_user_cannot_access_employees(): void
    {
        $response = $this->getJson('/employees');

        $response->assertStatus(401);
    }

    public function test_cannot_create_employee_with_existing_user(): void
    {
        $user = User::factory()->create();
        Employee::factory()->create(['user_id' => $user->id]);

        $employeeData = [
            'user_id' => $user->id,
            'nip' => '987654321',
            'full_name' => 'Jane Doe',
            'join_date' => '2023-01-01',
        ];

        $response = $this->withToken($this->adminToken)
            ->postJson('/employees', $employeeData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id']);
    }
}