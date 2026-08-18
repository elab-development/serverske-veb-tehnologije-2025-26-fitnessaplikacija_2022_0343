<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: KREIRANJE TABELE
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            // wger_id povezuje lokalni zapis sa vežbom iz javnog wger API-ja (nullable jer admin može ručno dodati vežbu)
            $table->unsignedBigInteger('wger_id')->nullable()->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->json('muscles')->nullable();
            $table->json('equipment')->nullable();
            // legacy_source je namerno dodata kolona koja se uklanja kasnijom migracijom (demonstracija DROP COLUMN)
            $table->string('legacy_source')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
