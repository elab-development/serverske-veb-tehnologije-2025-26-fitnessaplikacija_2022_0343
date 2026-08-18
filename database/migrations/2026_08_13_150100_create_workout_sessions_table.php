<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: KREIRANJE TABELE
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            // FK ka users se dodaje tek u posebnoj migraciji (add_foreign_keys...)
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->timestamp('session_date');
            // notes je namerno kreirana kao kratak string, kasnije se menja u text (demonstracija CHANGE COLUMN)
            $table->string('notes', 255)->nullable();
            $table->unsignedSmallInteger('duration_min')->nullable();
            // draft = radna verzija napravljena u Planneru, completed = završen trening prikazan u Journal-u
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workout_sessions');
    }
};
