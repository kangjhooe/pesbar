<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin bisa akses semua
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Editor bisa akses editor dan penulis
        if ($user->isEditor() && in_array($role, ['editor', 'penulis'])) {
            return $next($request);
        }

        // Penulis hanya bisa akses penulis
        if ($user->isPenulis() && $role === 'penulis') {
            return $next($request);
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
