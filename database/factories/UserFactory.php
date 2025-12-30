<?php

declare(strict_types=1);

namespace Database\Factories;

use Gomu\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Gomu\Auth\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'username' => $this->faker->unique()->userName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => md5('password'),
            'user_type' => $this->faker->randomElement(['internal', 'external']),
            'force_password_change' => false,
        ];
    }

    public function internal(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'internal',
        ]);
    }

    public function external(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'external',
        ]);
    }
}