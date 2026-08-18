<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: BRISANJE KOLONE
// legacy_source je služila samo za jednokratnu migraciju starih seed podataka i više nije potrebna.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn('legacy_source');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->string('legacy_source')->nullable();
        });
    }
};
