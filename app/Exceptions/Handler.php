<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

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

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // Handle different types of exceptions
        if ($exception instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($request, $exception);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->handleAuthenticationException($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->handleAuthorizationException($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->handleValidationException($request, $exception);
        }

        if ($exception instanceof TokenMismatchException) {
            return $this->handleTokenMismatchException($request, $exception);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($request, $exception);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedHttpException($request, $exception);
        }

        if ($exception instanceof AccessDeniedHttpException) {
            return $this->handleAccessDeniedHttpException($request, $exception);
        }

        if ($exception instanceof HttpException) {
            return $this->handleHttpException($request, $exception);
        }

        // For development environment, show detailed error
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        // For production, show generic 500 error
        return $this->handleGenericException($request, $exception);
    }

    /**
     * Handle ModelNotFoundException
     */
    protected function handleModelNotFoundException(Request $request, ModelNotFoundException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data tidak ditemukan.',
                'error' => 'Model not found'
            ], 404);
        }

        return response()->view('errors.404', [
            'exception' => $exception
        ], 404);
    }

    /**
     * Handle AuthenticationException
     */
    protected function handleAuthenticationException(Request $request, AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda harus login terlebih dahulu.',
                'error' => 'Unauthenticated'
            ], 401);
        }

        // If it's a direct request to a protected route, show 401 error page
        if ($request->is('admin/*') || $request->is('dashboard') || $request->is('profile/*')) {
            return response()->view('errors.401', [
                'exception' => $exception
            ], 401);
        }

        return redirect()->guest(route('login'));
    }

    /**
     * Handle AuthorizationException
     */
    protected function handleAuthorizationException(Request $request, AuthorizationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
                'error' => 'Forbidden'
            ], 403);
        }

        return response()->view('errors.403', [
            'exception' => $exception
        ], 403);
    }

    /**
     * Handle ValidationException
     */
    protected function handleValidationException(Request $request, ValidationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Data yang dimasukkan tidak valid.',
                'errors' => $exception->errors()
            ], 422);
        }

        // For login/register forms, redirect back with errors
        if ($request->is('login') || $request->is('register') || $request->is('password/*')) {
            return redirect()->back()
                ->withInput($request->input())
                ->withErrors($exception->errors());
        }

        // For other validation errors, show 422 error page
        return response()->view('errors.422', [
            'exception' => $exception,
            'errors' => $exception->errors()
        ], 422);
    }

    /**
     * Handle TokenMismatchException
     */
    protected function handleTokenMismatchException(Request $request, TokenMismatchException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Token CSRF tidak valid. Silakan refresh halaman dan coba lagi.',
                'error' => 'Token mismatch'
            ], 419);
        }

        return redirect()->back()
            ->withInput($request->input())
            ->with('error', 'Token CSRF tidak valid. Silakan refresh halaman dan coba lagi.');
    }

    /**
     * Handle NotFoundHttpException
     */
    protected function handleNotFoundHttpException(Request $request, NotFoundHttpException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Halaman yang Anda cari tidak ditemukan.',
                'error' => 'Not found'
            ], 404);
        }

        return response()->view('errors.404', [
            'exception' => $exception
        ], 404);
    }

    /**
     * Handle MethodNotAllowedHttpException
     */
    protected function handleMethodNotAllowedHttpException(Request $request, MethodNotAllowedHttpException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Method tidak diizinkan untuk URL ini.',
                'error' => 'Method not allowed'
            ], 405);
        }

        return response()->view('errors.405', [
            'exception' => $exception
        ], 405);
    }

    /**
     * Handle AccessDeniedHttpException
     */
    protected function handleAccessDeniedHttpException(Request $request, AccessDeniedHttpException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Akses ditolak.',
                'error' => 'Access denied'
            ], 403);
        }

        return response()->view('errors.403', [
            'exception' => $exception
        ], 403);
    }

    /**
     * Handle HttpException
     */
    protected function handleHttpException(Request $request, HttpException $exception): Response
    {
        $statusCode = $exception->getStatusCode();
        
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Terjadi kesalahan.',
                'error' => 'HTTP Exception'
            ], $statusCode);
        }

        // Check if custom error view exists
        if (view()->exists("errors.{$statusCode}")) {
            return response()->view("errors.{$statusCode}", [
                'exception' => $exception
            ], $statusCode);
        }

        // Fallback to generic error
        return $this->handleGenericException($request, $exception);
    }

    /**
     * Handle generic exceptions
     */
    protected function handleGenericException(Request $request, Throwable $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                'error' => 'Internal server error'
            ], 500);
        }

        return response()->view('errors.500', [
            'exception' => $exception
        ], 500);
    }
}
