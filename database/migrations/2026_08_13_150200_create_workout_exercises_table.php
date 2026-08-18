<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: KREIRANJE TABELE
// Ovo je "pivot+" tabela: spaja workout_sessions i exercises (many-to-many),
// ali nosi i dodatne podatke (sets/reps/weight) pa je poseban Eloquent model, ne classic pivot.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workout_session_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedSmallInteger('sets');
            $table->unsignedSmallInteger('reps');
            $table->decimal('weight', 6, 2)->nullable();
            $table->string('notes')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_exercises');
    }
};
