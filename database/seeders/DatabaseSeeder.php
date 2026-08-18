<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Pravi po jednog admin/member naloga za lako testiranje kroz Postman + demo podatke.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@fittrack.test',
        ]);

        $member = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@fittrack.test',
        ]);

        $exercises = Exercise::factory(15)->create();

        WorkoutSession::factory(6)
            ->for($member)
            ->create()
            ->each(function (WorkoutSession $session) use ($exercises) {
                $session->items()->createMany(
                    $exercises->random(rand(2, 4))->values()->map(fn ($ex, $i) => [
                        'exercise_id' => $ex->id,
                        'sets' => rand(2, 5),
                        'reps' => rand(6, 15),
                        'weight' => rand(10, 100),
                        'order' => $i,
                    ])->all()
                );
            });
    }
}
