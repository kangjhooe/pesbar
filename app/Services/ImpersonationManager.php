<?php

namespace App\Services;

use App\Helpers\ActivityLogHelper;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ImpersonationManager
{
    public const SESSION_ID = 'impersonator_id';
    public const SESSION_NAME = 'impersonator_name';

    public static function isImpersonating(): bool
    {
        return session()->has(self::SESSION_ID);
    }

    public static function impersonatorId(): ?int
    {
        $id = session(self::SESSION_ID);

        return $id !== null ? (int) $id : null;
    }

    public static function impersonatorName(): ?string
    {
        return session(self::SESSION_NAME);
    }

    /**
     * Start viewing the app as a penulis. Caller must already be an admin.
     */
    public static function start(User $admin, User $target): void
    {
        if (!$admin->isAdmin()) {
            throw new RuntimeException('Hanya admin yang dapat melakukan impersonasi.');
        }

        if (self::isImpersonating()) {
            throw new RuntimeException('Anda sedang dalam sesi impersonasi. Akhiri dulu sebelum memulai yang baru.');
        }

        if ($admin->id === $target->id) {
            throw new RuntimeException('Tidak dapat mengimpersonasi akun sendiri.');
        }

        if (!$target->isPenulis()) {
            throw new RuntimeException('Impersonasi hanya diizinkan untuk akun penulis.');
        }

        ActivityLogHelper::logSecurity('impersonation.start', 'Admin mulai impersonasi penulis', [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'target_user_id' => $target->id,
            'target_user_name' => $target->name,
            'target_user_email' => $target->email,
        ]);

        // Auth::login regenerates the session; store impersonator keys afterwards.
        Auth::login($target);

        session()->put([
            self::SESSION_ID => $admin->id,
            self::SESSION_NAME => $admin->name,
        ]);
    }

    /**
     * Restore the original admin session.
     */
    public static function leave(): User
    {
        if (!self::isImpersonating()) {
            throw new RuntimeException('Tidak ada sesi impersonasi yang aktif.');
        }

        $adminId = self::impersonatorId();
        $admin = User::find($adminId);

        if (!$admin || !$admin->isAdmin()) {
            session()->forget([self::SESSION_ID, self::SESSION_NAME]);
            Auth::logout();
            throw new RuntimeException('Akun admin asal tidak valid. Silakan login ulang.');
        }

        $asUser = Auth::user();

        ActivityLogHelper::logSecurity('impersonation.leave', 'Admin mengakhiri impersonasi penulis', [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'target_user_id' => $asUser?->id,
            'target_user_name' => $asUser?->name,
        ]);

        session()->forget([self::SESSION_ID, self::SESSION_NAME]);
        Auth::login($admin);

        return $admin;
    }
}
