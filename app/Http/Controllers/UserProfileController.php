<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Helpers\ActivityLogHelper;
use App\Helpers\UploadValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::with('profile')
            ->where('username', $username)
            ->firstOrFail();
        
        if (!$user->isPenulis()) {
            abort(404);
        }

        $articles = $user->articles()
            ->published()
            ->with('category')
            ->latest('published_at')
            ->paginate(12);

        // Statistik publik: hanya artikel yang benar-benar terbit (status + published_at).
        $stats = [
            'total_articles' => $user->articles()->published()->count(),
            'total_views' => $user->articles()->published()->sum('views'),
            'total_comments' => $user->articles()->published()->withCount('comments')->get()->sum('comments_count'),
        ];

        // Check if current user is admin
        $isAdmin = Auth::check() && Auth::user()->isAdmin();

        return view('penulis.public-profile', compact('user', 'articles', 'stats', 'isAdmin'));
    }

    public function upgradeRequest()
    {
        $user = Auth::user();

        if ($user->isPenulis()) {
            return redirect()->route('penulis.dashboard')
                ->with('info', 'Anda sudah menjadi penulis terverifikasi.');
        }

        if ($user->isBanned()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Anda sedang dibanned dan tidak dapat mengajukan upgrade hingga ' . $user->banned_until->format('d M Y, H:i') . '.');
        }

        if ($user->hasPendingUpgradeRequest()) {
            return redirect()->route('user.dashboard')
                ->with('info', 'Anda sudah memiliki permintaan upgrade yang sedang ditinjau. Mohon tunggu konfirmasi dari admin.');
        }

        if (!$user->canRequestUpgrade()) {
            return redirect()->back()->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
        }

        return view('user.upgrade-request');
    }

    public function submitUpgradeRequest(Request $request)
    {
        try {
            $request->validate([
                'verification_type' => 'required|in:perorangan,lembaga',
                'verification_document' => UploadValidation::verificationDocument(true, 5120),
                'bio' => 'required|string|max:1000',
                'avatar' => UploadValidation::image(false, 2048),
                'website' => 'nullable|url',
                'location' => 'nullable|string|max:255',
                'social_links' => 'nullable|array',
            ]);

            $user = Auth::user();

            if ($user->isBanned()) {
                return redirect()->route('user.dashboard')
                    ->with('error', 'Anda sedang dibanned dan tidak dapat mengajukan upgrade.');
            }

            if ($user->role !== 'user') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
            }

            if ($user->verification_request_status === 'pending') {
                return redirect()->route('user.dashboard')
                    ->with('info', 'Anda sudah memiliki permintaan upgrade yang sedang ditinjau. Mohon tunggu konfirmasi dari admin.');
            }

            $verificationDocumentPath = null;
            if ($request->hasFile('verification_document')) {
                if ($user->verification_document && Storage::disk('public')->exists($user->verification_document)) {
                    Storage::disk('public')->delete($user->verification_document);
                }

                $verificationDocumentPath = $request->file('verification_document')->store('upgrade-documents', 'public');
            }

            // Gate ketat: role tetap 'user' sampai admin menyetujui
            $user->update([
                'verification_type' => $request->verification_type,
                'verification_document' => $verificationDocumentPath,
                'verification_requested_at' => now(),
                'verification_request_status' => 'pending',
                'verification_rejection_reason' => null,
                'verified' => false,
            ]);

            $data = [
                'user_id' => $user->id,
                'bio' => $request->bio,
                'website' => $request->website,
                'location' => $request->location,
                'social_links' => $request->social_links,
            ];

            if ($request->hasFile('avatar')) {
                if ($user->profile && $user->profile->avatar) {
                    Storage::disk('public')->delete($user->profile->avatar);
                }
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if ($user->profile) {
                $user->profile->update($data);
            } else {
                UserProfile::create($data);
            }

            ActivityLogHelper::logUser('upgrade.requested', $user, "User {$user->name} mengajukan permintaan upgrade ke penulis");
            ActivityLogHelper::logSecurity('upgrade.requested', 'Permintaan upgrade ke penulis', [
                'user_id' => $user->id,
                'verification_type' => $request->verification_type
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', 'Permintaan upgrade ke penulis berhasil dikirim. Akun Anda tetap sebagai pembaca sampai admin menyetujui.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Upgrade Request Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            ActivityLogHelper::logSecurity('upgrade.request.failed', 'Gagal submit upgrade request', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim permintaan upgrade. Silakan coba lagi.');
        }
    }
}
