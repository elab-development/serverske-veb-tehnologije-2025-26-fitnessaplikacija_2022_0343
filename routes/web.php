<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ova aplikacija nema web login formu (samo API). Ruta postoji samo da bi imenovana
// ruta 'login' bila definisana — Laravel-ov auth middleware je traži za redirect kad
// klijent ne šalje Accept: application/json (npr. otvaranje URL-a direktno u browseru).
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');
