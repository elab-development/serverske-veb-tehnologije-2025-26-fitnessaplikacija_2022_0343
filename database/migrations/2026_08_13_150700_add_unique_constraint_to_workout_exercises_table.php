<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: POSTAVLJANJE DODATNIH OGRANIČENJA
// Sprečava da ista vežba zauzme dva mesta (order) u istom treningu.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->unique(
                ['workout_session_id', 'exercise_id', 'order'],
                'workout_exercise_unique_slot'
            );
        });
    }

    public function down(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->dropUnique('workout_exercise_unique_slot');
        });
    }
};
