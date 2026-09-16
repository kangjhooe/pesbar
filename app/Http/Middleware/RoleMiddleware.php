<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Strict role gate: user may pass only if their role is in the allowed list.
     * Supports comma-separated roles, e.g. role:admin,editor.
     * Admin/editor no longer bypass into /penulis.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Refresh so role demotions apply while still logged in
        $user->refresh();

        if (!$user->role || trim($user->role) === '') {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        $allowed = [];
        foreach ($roles as $roleParam) {
            foreach (explode(',', $roleParam) as $role) {
                $role = trim($role);
                if ($role !== '') {
                    $allowed[] = $role;
                }
            }
        }

        if (in_array($user->role, $allowed, true)) {
            return $next($request);
        }

        // Soft redirect for demoted / ordinary users hitting penulis area
        if (in_array('penulis', $allowed, true) && $user->role === 'user') {
            return redirect()->route('user.dashboard')
                ->with('error', 'Akses ditolak. Anda tidak lagi memiliki akses sebagai penulis.');
        }

        // Soft redirect: higher roles must not fall into the user dashboard
        if (in_array('user', $allowed, true)) {
            if ($user->isAdmin() || $user->isEditor()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isPenulis()) {
                return redirect()->route('penulis.dashboard');
            }
        }

        abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}
