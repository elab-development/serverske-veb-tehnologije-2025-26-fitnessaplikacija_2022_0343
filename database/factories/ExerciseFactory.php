<?php

namespace Database\Factories;

use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'wger_id' => fake()->unique()->numberBetween(1, 100000),
            'name' => ucfirst(fake()->words(2, true)),
            'category' => fake()->randomElement(['Arms', 'Legs', 'Chest', 'Back', 'Core', 'Cardio']),
            'muscles' => fake()->randomElements(['Biceps', 'Triceps', 'Quads', 'Hamstrings', 'Glutes', 'Abs'], 2),
            'equipment' => fake()->randomElements(['Barbell', 'Dumbbell', 'Bodyweight', 'Machine'], 1),
        ];
    }
}
