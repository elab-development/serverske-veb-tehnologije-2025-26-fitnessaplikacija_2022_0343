<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\InsightsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkoutExerciseController;
use App\Http\Controllers\Api\WorkoutSessionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Javne rute (guest / neulogovan korisnik)
|--------------------------------------------------------------------------
| throttle:10,1 na auth rutama = max 10 pokušaja u minuti po IP-ju,
| dodatna zaštita od brute-force napada na login/register.
*/
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Katalog vežbi je javno vidljiv (Exercises stranica radi i bez logovanja)
Route::get('/exercises', [ExerciseController::class, 'index']);
Route::get('/exercises/{exercise}', [ExerciseController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rute za ulogovane korisnike (uloga: member ili admin)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);

    Route::get('/insights/summary', [InsightsController::class, 'summary']);

    // Mora ići PRE apiResource-a, inače bi 'export' bio protumačen kao {workout_session} id
    Route::get('/workout-sessions/export', [WorkoutSessionController::class, 'export']);

    // Resource ruta (zahtev: najmanje jedna resource ruta) — index/store/show/update/destroy
    Route::apiResource('workout-sessions', WorkoutSessionController::class);

    // Ugnježdene rute (zahtev: najmanje 2 ugnježdene rute)
    Route::get('/workout-sessions/{workoutSession}/exercises', [WorkoutExerciseController::class, 'index']);
    Route::post('/workout-sessions/{workoutSession}/exercises', [WorkoutExerciseController::class, 'store']);
    Route::put('/workout-sessions/{workoutSession}/exercises/{exercise}', [WorkoutExerciseController::class, 'update']);
    Route::delete('/workout-sessions/{workoutSession}/exercises/{exercise}', [WorkoutExerciseController::class, 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Admin-only rute (uloga: admin)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::post('/exercises', [ExerciseController::class, 'store']);
        Route::put('/exercises/{exercise}', [ExerciseController::class, 'update']);
        Route::delete('/exercises/{exercise}', [ExerciseController::class, 'destroy']);
        Route::post('/exercises/sync', [ExerciseController::class, 'sync']);
    });
});
