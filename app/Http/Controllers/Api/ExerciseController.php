<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Models\Exercise;
use App\Services\WgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

// Resource kontroler (index/show/store/update/destroy) za katalog vežbi.
// index/show su javni (dostupni i neulogovanom korisniku); store/update/destroy su admin-only (vidi routes/api.php).
class ExerciseController extends Controller
{
    /**
     * GET /api/exercises
     * Podržava: pretragu (search), filtriranje (category, muscle), sortiranje (sort) i paginaciju (per_page).
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($muscle = $request->query('muscle')) {
            // JSON kolona: proveravamo da li se traženi mišić nalazi u nizu muscles
            $query->whereJsonContains('muscles', $muscle);
        }

        $sort = in_array($request->query('sort'), ['name', 'category', 'created_at']) ? $request->query('sort') : 'name';
        $query->orderBy($sort);

        $perPage = min((int) $request->query('per_page', 20), 100);

        return response()->json($query->paginate($perPage));
    }

    public function show(Exercise $exercise)
    {
        return response()->json($exercise);
    }

    public function store(StoreExerciseRequest $request)
    {
        $exercise = Exercise::create($request->validated());

        return response()->json($exercise, 201);
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise)
    {
        $exercise->update($request->validated());

        return response()->json($exercise);
    }

    public function destroy(Exercise $exercise)
    {
        $exercise->delete();

        return response()->json(null, 204);
    }

    /**
     * POST /api/exercises/sync
     * Poziva javni wger.de API i upisuje/ažurira vežbe u lokalnu bazu (admin-only).
     * Rezultat se kešira 1h po (limit, offset) kombinaciji da ne opterećujemo eksterni servis.
     */
    public function sync(Request $request, WgerService $wger)
    {
        $limit = min((int) $request->query('limit', 20), 50);
        $offset = (int) $request->query('offset', 0);

        $page = Cache::remember("wger:page:{$limit}:{$offset}", 3600, function () use ($wger, $limit, $offset) {
            return $wger->fetchExercisePage($limit, $offset);
        });

        $created = 0;
        $updated = 0;

        foreach ($page['items'] as $item) {
            $exercise = Exercise::updateOrCreate(['wger_id' => $item['wger_id']], $item);
            $exercise->wasRecentlyCreated ? $created++ : $updated++;
        }

        return response()->json([
            'created' => $created,
            'updated' => $updated,
            'hasMore' => $page['hasMore'],
        ]);
    }
}
