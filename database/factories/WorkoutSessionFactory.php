<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutSession>
 */
class WorkoutSessionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement(['Push Day', 'Pull Day', 'Leg Day', 'Full Body', 'Upper Body']),
            'session_date' => fake()->dateTimeBetween('-2 months', 'now'),
            'notes' => fake()->optional()->sentence(),
            'duration_min' => fake()->numberBetween(20, 90),
            'status' => fake()->randomElement(['draft', 'completed']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'completed']);
    }
}
