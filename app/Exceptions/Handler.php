<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Handle 404 errors
        if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            return response()->view('pages.errors.page-error-404', [], 404);
        }
        
        // Handle 403 Forbidden errors
        if ($exception instanceof HttpException && $exception->getStatusCode() === 403) {
            return response()->view('pages.errors.page-error-403', [], 403);
        }
        
        // Handle 400 Bad Request errors
        if ($exception instanceof BadRequestHttpException) {
            return response()->view('pages.errors.page-error-400', [], 400);
        }
        
        // Handle 503 Service Unavailable errors
        if ($exception instanceof ServiceUnavailableHttpException) {
            return response()->view('pages.errors.page-error-503', [], 503);
        }
        
        // Handle 500 errors - always use the custom template
        if ($exception instanceof HttpException && $exception->getStatusCode() === 500 || 
            $request->is('admin/error-test/500')) {
            return response()->view('pages.errors.page-error-500', [], 500);
        }
        
        // For other 500 errors (general exceptions)
        if ($exception instanceof \Exception || $exception instanceof \Error) {
            // In production, always show the custom error page
            if (!config('app.debug')) {
                return response()->view('pages.errors.page-error-500', [], 500);
            }
            // In debug mode, only show custom error for specific test routes
            elseif ($request->is('admin/error-test/*')) {
                return response()->view('pages.errors.page-error-500', [], 500);
            }
        }

        return parent::render($request, $exception);
    }
}
