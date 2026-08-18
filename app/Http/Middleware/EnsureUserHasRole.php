<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Middleware koji ograničava rutu na korisnike sa određenom ulogom (npr. role:admin).
// Neulogovan korisnik nikad ne prolazi (auth:sanctum se poziva pre ovoga u route grupi).
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            return response()->json([
                'message' => 'Forbidden: this action requires the "'.$role.'" role.',
            ], 403);
        }

        return $next($request);
    }
}
