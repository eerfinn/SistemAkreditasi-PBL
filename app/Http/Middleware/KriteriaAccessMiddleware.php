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

            // Check if the user has access to this kriteria (for all roles except admin)
            if ($user->role !== 'administrator' && !$user->hasKriteriaAccess($kriteriaId)) {
                abort(403, 'Forbidden: You do not have access to this kriteria.');
            }
        }

        // For upload form access
        if ($request->routeIs('kriteria.upload.form')) {
            if (!in_array($user->role, ['administrator', 'dosen', 'koordinator', 'kjm', 'kaprodi', 'kajur'])) {
                abort(403, 'Forbidden: You do not have permission to manage documents.');
            }

            // Additional check for kriteria access when uploading
            $kriteriaId = $request->route('kriteria')->id ?? $request->route('kriteria');

            // Check if the user has access to this kriteria (for all roles except admin)
            if ($user->role !== 'administrator' && !$user->hasKriteriaAccess($kriteriaId)) {
                abort(403, 'Forbidden: You do not have access to this kriteria.');
            }
        }

        // For validation access
        if ($request->routeIs('validasi.*')) {
            // For document validation
            if ($request->routeIs('validasi.update-status') && !in_array($user->role, ['administrator', 'koordinator', 'direktur'])) {
                abort(403, 'Forbidden: Only administrators, coordinators, and directors can validate documents.');
            }

            // For kriteria comments
            if ($request->routeIs('validasi.kriteria-comment') && !in_array($user->role, ['administrator', 'koordinator', 'kajur', 'kaprodi', 'direktur'])) {
                abort(403, 'Forbidden: Only administrators, coordinators, directors, kajur, and kaprodi can add comments to kriteria.');
            }
        }

        return $next($request);
    }
}
