<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkoutSessionRequest;
use App\Http\Requests\UpdateWorkoutSessionRequest;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Resource kontroler: index/show/store/update/destroy za treninge (Planner + Journal na frontendu).
// Svaki korisnik vidi i menja samo svoje treninge; admin vidi sve (?all=1).
class WorkoutSessionController extends Controller
{
    /**
     * GET /api/workout-sessions
     * Filtriranje po statusu (draft/completed) i paginacija; admin može ?all=1 da vidi sve korisnike.
     */
    public function index(Request $request)
    {
        $query = WorkoutSession::with('items.exercise')->latest('session_date');

        if ($request->user()->isAdmin() && $request->boolean('all')) {
            // admin uvid u sve treninge svih korisnika
        } else {
            $query->where('user_id', $request->user()->id);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min((int) $request->query('per_page', 10), 100);

        return response()->json($query->paginate($perPage));
    }

    /**
     * POST /api/workout-sessions
     * Kreira trening i (opciono) njegove stavke u jednoj DB transakciji:
     * ako ubacivanje neke stavke pukne, ceo trening se poništava (rollback).
     */
    public function store(StoreWorkoutSessionRequest $request)
    {
        $data = $request->validated();
        $items = $data['items'] ?? [];
        unset($data['items']);

        $session = DB::transaction(function () use ($request, $data, $items) {
            $session = WorkoutSession::create([
                ...$data,
                'user_id' => $request->user()->id,
                'status' => $data['status'] ?? 'draft',
            ]);

            foreach ($items as $i => $item) {
                $session->items()->create([...$item, 'order' => $item['order'] ?? $i]);
            }

            return $session;
        });

        return response()->json($session->load('items.exercise'), 201);
    }

    public function show(Request $request, WorkoutSession $workoutSession)
    {
        $this->authorizeAccess($request, $workoutSession);

        return response()->json($workoutSession->load('items.exercise'));
    }

    public function update(UpdateWorkoutSessionRequest $request, WorkoutSession $workoutSession)
    {
        $this->authorizeAccess($request, $workoutSession);

        $workoutSession->update($request->validated());

        return response()->json($workoutSession->load('items.exercise'));
    }

    public function destroy(Request $request, WorkoutSession $workoutSession)
    {
        $this->authorizeAccess($request, $workoutSession);
        $workoutSession->delete();

        return response()->json(null, 204);
    }

    /**
     * GET /api/workout-sessions/export
     * Izvoz istorije treninga korisnika u CSV (dodatna funkcionalnost: export podataka).
     */
    public function export(Request $request): StreamedResponse
    {
        $sessions = WorkoutSession::with('items.exercise')
            ->where('user_id', $request->user()->id)
            ->orderBy('session_date')
            ->get();

        $filename = 'workout-history-'.now()->format('Y-m-d').'.csv';

        $callback = function () use ($sessions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Title', 'Status', 'Duration (min)', 'Exercise', 'Sets', 'Reps', 'Weight']);

            foreach ($sessions as $session) {
                if ($session->items->isEmpty()) {
                    fputcsv($out, [$session->session_date, $session->title, $session->status, $session->duration_min, '', '', '', '']);
                    continue;
                }
                foreach ($session->items as $item) {
                    fputcsv($out, [
                        $session->session_date, $session->title, $session->status, $session->duration_min,
                        $item->exercise->name ?? '', $item->sets, $item->reps, $item->weight,
                    ]);
                }
            }

            fclose($out);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Vlasnik treninga ili admin — svi ostali dobijaju 403 (IDOR zaštita: korisnik ne može
     * pogađati tuđe ID-jeve treninga da bi im pristupio).
     */
    private function authorizeAccess(Request $request, WorkoutSession $session): void
    {
        abort_unless(
            $session->user_id === $request->user()->id || $request->user()->isAdmin(),
            403,
            'You do not have access to this workout session.'
        );
    }
}
