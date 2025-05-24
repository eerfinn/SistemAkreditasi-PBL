<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KriteriaAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is logged in
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Check for kriteria access restrictions based on role
        if ($request->route('kriteria')) {
            $kriteriaId = $request->route('kriteria')->id ?? $request->route('kriteria');

            // Role-based access restrictions for dosen roles
            if ($user->role === 'dosen1' && !in_array($kriteriaId, [1, 2, 3])) {
                abort(403, 'Forbidden: Dosen 1 can only access Kriteria 1-3.');
            }

            if ($user->role === 'dosen2' && !in_array($kriteriaId, [4, 5, 6])) {
                abort(403, 'Forbidden: Dosen 2 can only access Kriteria 4-6.');
            }

            if ($user->role === 'dosen3' && !in_array($kriteriaId, [7, 8, 9])) {
                abort(403, 'Forbidden: Dosen 3 can only access Kriteria 7-9.');
            }
        }

        // For upload form access
        if ($request->routeIs('kriteria.upload.form')) {
            if (!in_array($user->role, ['administrator', 'dosen1', 'dosen2', 'dosen3'])) {
                abort(403, 'Forbidden: Only administrators and lecturers can manage documents.');
            }

            // Additional check for kriteria access when uploading
            $kriteriaId = $request->route('kriteria')->id ?? $request->route('kriteria');

            if ($user->role === 'dosen1' && !in_array($kriteriaId, [1, 2, 3])) {
                abort(403, 'Forbidden: Dosen 1 can only access Kriteria 1-3.');
            }

            if ($user->role === 'dosen2' && !in_array($kriteriaId, [4, 5, 6])) {
                abort(403, 'Forbidden: Dosen 2 can only access Kriteria 4-6.');
            }

            if ($user->role === 'dosen3' && !in_array($kriteriaId, [7, 8, 9])) {
                abort(403, 'Forbidden: Dosen 3 can only access Kriteria 7-9.');
            }
        }

        // For validation access
        if ($request->routeIs('validasi.*')) {
            // For document validation
            if ($request->routeIs('validasi.update-status') && !in_array($user->role, ['administrator', 'koordinator'])) {
                abort(403, 'Forbidden: Only administrators and coordinators can validate documents.');
            }

            // For kriteria comments
            if ($request->routeIs('validasi.kriteria-comment') && !in_array($user->role, ['administrator', 'koordinator', 'kajur', 'kaprodi'])) {
                abort(403, 'Forbidden: Only administrators, coordinators, kajur, and kaprodi can add comments to kriteria.');
            }
        }

        return $next($request);
    }
}
