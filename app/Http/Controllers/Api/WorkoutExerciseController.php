<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkoutExerciseItemRequest;
use App\Models\WorkoutExercise;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;

// Ugnježdene rute: /api/workout-sessions/{workoutSession}/exercises[/{workoutExercise}]
// Stavke (vežbe unutar treninga) uvek žive u kontekstu svog roditeljskog treninga.
class WorkoutExerciseController extends Controller
{
    public function index(Request $request, WorkoutSession $workoutSession)
    {
        $this->authorizeAccess($request, $workoutSession);

        return response()->json($workoutSession->items()->with('exercise')->get());
    }

    public function store(WorkoutExerciseItemRequest $request, WorkoutSession $workoutSession)
    {
        $this->authorizeAccess($request, $workoutSession);

        $data = $request->validated();
        $data['order'] ??= $workoutSession->items()->max('order') + 1;

        $item = $workoutSession->items()->create($data);

        return response()->json($item->load('exercise'), 201);
    }

    public function update(WorkoutExerciseItemRequest $request, WorkoutSession $workoutSession, WorkoutExercise $exercise)
    {
        $this->authorizeAccess($request, $workoutSession);
        abort_unless($exercise->workout_session_id === $workoutSession->id, 404);

        $exercise->update($request->validated());

        return response()->json($exercise->load('exercise'));
    }

    public function destroy(Request $request, WorkoutSession $workoutSession, WorkoutExercise $exercise)
    {
        $this->authorizeAccess($request, $workoutSession);
        abort_unless($exercise->workout_session_id === $workoutSession->id, 404);

        $exercise->delete();

        return response()->json(null, 204);
    }

    private function authorizeAccess(Request $request, WorkoutSession $session): void
    {
        abort_unless(
            $session->user_id === $request->user()->id || $request->user()->isAdmin(),
            403,
            'You do not have access to this workout session.'
        );
    }
}
