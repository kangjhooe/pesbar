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
        
        // Refresh user dari database untuk memastikan data terbaru (terutama role)
        // Ini penting ketika role user berubah saat mereka masih login
        $user->refresh();
        
        // Pastikan user memiliki role yang valid
        if (!$user->role || trim($user->role) === '') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        // PRIORITAS 1: Cek mantan penulis yang mencoba akses route penulis
        // Ini harus dicek SEBELUM pengecekan admin/editor untuk mencegah bypass
        // User biasa (bukan admin, bukan editor, bukan penulis) yang mencoba akses route penulis
        if ($role === 'penulis' && !$user->isPenulis() && !$user->isAdmin() && !$user->isEditor()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak lagi memiliki akses sebagai penulis.');
        }
        
        // PRIORITAS 2: Admin bisa akses semua
        if ($user->isAdmin()) {
            return $next($request);
        }

        // PRIORITAS 3: Editor bisa akses editor dan penulis
        if ($user->isEditor() && in_array($role, ['editor', 'penulis'])) {
            return $next($request);
        }

        // PRIORITAS 4: Penulis hanya bisa akses penulis
        if ($user->isPenulis() && $role === 'penulis') {
            return $next($request);
        }

        // Jika tidak memenuhi kondisi di atas, tolak akses
        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
