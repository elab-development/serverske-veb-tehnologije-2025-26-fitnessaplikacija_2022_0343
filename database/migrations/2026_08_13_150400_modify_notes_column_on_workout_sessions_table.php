<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: IZMENA POSTOJEĆE KOLONE
// notes je pravljena kao string(255); korisnici su tražili duže beleške po treningu, pa menjamo tip u TEXT.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->text('notes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->string('notes', 255)->nullable()->change();
        });
    }
};
