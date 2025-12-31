<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();
        
        // Refresh user dari database untuk memastikan data terbaru (terutama role)
        // Ini penting ketika role user berubah saat mereka masih login
        $user->refresh();
        
        // Redirect berdasarkan role
        if ($user->isAdmin() || $user->isEditor()) {
            return redirect()->intended(route('dashboard', absolute: false));
        } elseif ($user->isPenulis()) {
            return redirect()->intended(route('penulis.dashboard', absolute: false));
        } else {
            // User biasa (termasuk mantan penulis yang verifikasinya dibatalkan)
            return redirect()->intended(route('user.dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
