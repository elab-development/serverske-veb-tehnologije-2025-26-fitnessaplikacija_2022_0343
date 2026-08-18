<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkoutExercise>
 */
class WorkoutExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workout_session_id' => WorkoutSession::factory(),
            'exercise_id' => Exercise::factory(),
            'sets' => fake()->numberBetween(2, 5),
            'reps' => fake()->numberBetween(6, 15),
            'weight' => fake()->randomFloat(2, 0, 120),
            'notes' => fake()->optional()->sentence(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
