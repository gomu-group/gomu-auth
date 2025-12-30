<?php

declare(strict_types=1);

namespace Database\Factories;

use Gomu\Auth\Models\Employee;
use Gomu\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Gomu\Auth\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'user_id' => User::factory(),
            'nip' => $this->faker->unique()->numerify('##########'),
            'full_name' => $this->faker->name(),
            'join_date' => $this->faker->date(),
            'department_id' => null, // Can be set later
            'job_level_id' => null,
            'job_position_id' => null,
        ];
    }
}