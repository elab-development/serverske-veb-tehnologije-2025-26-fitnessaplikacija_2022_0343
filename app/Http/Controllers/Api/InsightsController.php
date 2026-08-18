<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSession;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Agregirani podaci za Insights stranicu na frontendu (grafici volumena i treninga po danu).
class InsightsController extends Controller
{
    public function summary(Request $request, WeatherService $weather)
    {
        $userId = $request->user()->id;

        // Ukupan broj završenih treninga i ukupan "volume" (sets * reps * weight), JOIN preko 3 tabele.
        $totals = DB::table('workout_sessions')
            ->join('workout_exercises', 'workout_exercises.workout_session_id', '=', 'workout_sessions.id')
            ->where('workout_sessions.user_id', $userId)
            ->selectRaw('COUNT(DISTINCT workout_sessions.id) as total_sessions')
            ->selectRaw('COALESCE(SUM(workout_exercises.sets * workout_exercises.reps * workout_exercises.weight), 0) as total_volume')
            ->first();

        // Volumen po nedelji (poslednjih 10 nedelja sa podacima) — GROUP BY + agregacija.
        $volumeByWeek = DB::table('workout_sessions')
            ->join('workout_exercises', 'workout_exercises.workout_session_id', '=', 'workout_sessions.id')
            ->where('workout_sessions.user_id', $userId)
            ->selectRaw("strftime('%Y-W%W', workout_sessions.session_date) as week")
            ->selectRaw('SUM(workout_exercises.sets * workout_exercises.reps * workout_exercises.weight) as volume')
            ->groupBy('week')
            ->orderBy('week')
            ->limit(10)
            ->get();

        // Broj treninga po danu u nedelji (0 = nedelja ... 6 = subota, isto kao JS Date.getDay()).
        $sessionsByWeekday = DB::table('workout_sessions')
            ->where('user_id', $userId)
            ->selectRaw("CAST(strftime('%w', session_date) AS INTEGER) as weekday")
            ->selectRaw('COUNT(*) as sessions')
            ->groupBy('weekday')
            ->get()
            ->keyBy('weekday');

        $weekdayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $byWeekday = collect($weekdayLabels)->map(fn ($label, $i) => [
            'day' => $label,
            'sessions' => (int) ($sessionsByWeekday[$i]->sessions ?? 0),
        ])->values();

        return response()->json([
            'total_sessions' => (int) $totals->total_sessions,
            'total_volume' => (float) $totals->total_volume,
            'volume_by_week' => $volumeByWeek,
            'sessions_by_weekday' => $byWeekday,
            // Poziv javnog Open-Meteo servisa: trenutna temperatura, kešira se u WeatherService.
            'current_temperature_c' => $weather->currentTemperature(),
        ]);
    }
}
