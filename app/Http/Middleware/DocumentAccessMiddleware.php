<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;
use Symfony\Component\HttpFoundation\Response;

class DocumentAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }

        // Get document ID from route parameter
        $documentId = $request->route('id');
        if (!$documentId) {
            abort(400, 'Document ID is required');
        }

        // Find the document
        $document = Dokumen::find($documentId);
        if (!$document) {
            abort(404, 'Document not found');
        }

        // Administrator has access to all documents
        if ($user->role === 'administrator') {
            return $next($request);
        }

        // Document owner always has access
        if ($document->user_id === $user->id) {
            return $next($request);
        }

        // Koordinator can access all non-draft documents
        if ($user->role === 'koordinator') {
            if ($document->status !== Dokumen::STATUS_DRAFT) {
                return $next($request);
            }
        }

        // Direktur can only access documents that have been validated by koordinator
        // or documents they've rejected or verified
        if ($user->role === 'direktur') {
            if ($document->status === Dokumen::STATUS_MENUNGGU_DIREKTUR ||
                $document->status === Dokumen::STATUS_DIVERIFIKASI ||
                ($document->status === Dokumen::STATUS_REVISI && $document->validator_level === 'direktur')) {
                return $next($request);
            }

            // If the document is not in a status the director can access, return a more specific error
            abort(403, 'This document is not yet ready for director review. It must first be validated by a coordinator.');
        }

        // Other roles (dosen, etc.) can access non-draft documents
        if ($document->status !== Dokumen::STATUS_DRAFT) {
            return $next($request);
        }

        // Default: access denied
        abort(403, 'You do not have permission to access this document');
    }
}
