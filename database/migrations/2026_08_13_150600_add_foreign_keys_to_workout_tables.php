<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: POSTAVLJANJE SPOLJNIH KLJUČEVA
// Namerno odvojeno od CREATE TABLE migracija da bi se jasno demonstrovao ovaj tip migracije.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade'); // brisanjem korisnika brišu se i njegovi treninzi
        });

        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->foreign('workout_session_id')
                ->references('id')->on('workout_sessions')
                ->onDelete('cascade'); // brisanjem treninga brišu se i njegove stavke
            $table->foreign('exercise_id')
                ->references('id')->on('exercises')
                ->onDelete('restrict'); // ne dozvoljava brisanje vežbe koja se koristi u treninzima
        });
    }

    public function down(): void
    {
        Schema::table('workout_exercises', function (Blueprint $table) {
            $table->dropForeign(['workout_session_id']);
            $table->dropForeign(['exercise_id']);
        });

        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }
};
