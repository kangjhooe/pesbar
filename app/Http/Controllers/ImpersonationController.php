<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImpersonationManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ImpersonationController extends Controller
{
    /**
     * Enter a penulis session (admin only; route already gated by role:admin).
     */
    public function start(User $user): RedirectResponse
    {
        try {
            ImpersonationManager::start(Auth::user(), $user);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('admin.penulis.index')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('penulis.dashboard')
            ->with('success', 'Anda sekarang melihat sebagai '.$user->name.'. Tindakan yang dilakukan berlaku atas nama penulis ini.');
    }

    /**
     * Leave impersonation and restore the admin session.
     */
    public function leave(): RedirectResponse
    {
        try {
            ImpersonationManager::leave();
        } catch (RuntimeException $e) {
            return redirect()
                ->route('login')
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.penulis.index')
            ->with('success', 'Sesi impersonasi diakhiri. Anda kembali sebagai admin.');
    }
}
