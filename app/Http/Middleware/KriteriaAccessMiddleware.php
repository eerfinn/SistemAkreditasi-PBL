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

        // For upload form access
        if ($request->routeIs('kriteria.upload.form')) {
            if (!in_array($user->role, ['administrator', 'dosen'])) {
                abort(403, 'Forbidden: Only administrators and lecturers can manage documents.');
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