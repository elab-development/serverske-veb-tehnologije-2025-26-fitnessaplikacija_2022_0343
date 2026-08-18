<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija tipa: DODAVANJE KOLONE
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // uloge: admin (puna kontrola) i member (obican ulogovan korisnik); neulogovan = nema token
            $table->enum('role', ['admin', 'member'])->default('member')->after('email');
            // avatar_path koristi feature za upload fajlova
            $table->string('avatar_path')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar_path']);
        });
    }
};
