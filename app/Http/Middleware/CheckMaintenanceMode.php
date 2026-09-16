<?php

namespace App\Http\Middleware;

use App\Helpers\SettingsHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Soft maintenance from admin settings.
     * Guests see 503; authenticated users (login, publish, komentar) tetap jalan.
     * Admin/editor always pass.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!SettingsHelper::maintenanceMode()) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && ($user->isAdmin() || $user->isEditor())) {
            return $next($request);
        }

        // Allow auth so admin can sign in and turn maintenance off
        if ($request->routeIs([
            'login',
            'logout',
            'register',
            'password.request',
            'password.email',
            'password.reset',
            'password.store',
            'password.update',
            'password.confirm',
        ]) || $request->is('up')) {
            return $next($request);
        }

        // Keep penulis/user flows intact while logged in
        if ($user) {
            return $next($request);
        }

        return response()->view('errors.maintenance', [
            'siteName' => SettingsHelper::siteName(),
        ], 503);
    }
}
