<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * POST /api/user/avatar
     * Upload fajla (profilna slika); čuva se na public disku, stari fajl se briše.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // max 2MB, mora biti slika
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_path' => $path]);

        return response()->json([
            'avatar_path' => $path,
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * GET /api/admin/users
     * Samo za admina (role:admin middleware) — pregled svih korisnika, sa paginacijom.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->query('per_page', 20), 100);

        return response()->json(User::orderBy('name')->paginate($perPage));
    }
}
