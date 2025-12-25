<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Helpers\ActivityLogHelper;
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
            ->where('status', 'published')
            ->with('category')
            ->latest()
            ->paginate(10);

        // Get statistics
        $stats = [
            'total_articles' => $user->articles()->where('status', 'published')->count(),
            'total_views' => $user->articles()->where('status', 'published')->sum('views'),
            'total_comments' => $user->articles()->where('status', 'published')->withCount('comments')->get()->sum('comments_count'),
        ];

        // Check if current user is admin
        $isAdmin = Auth::check() && Auth::user()->isAdmin();

        return view('penulis.public-profile', compact('user', 'articles', 'stats', 'isAdmin'));
    }

    public function upgradeRequest()
    {
        $user = Auth::user();
        
        // Cek jika user sudah menjadi penulis terverifikasi
        if ($user->role === 'penulis' && $user->verified) {
            return redirect()->route('penulis.dashboard')
                ->with('info', 'Anda sudah menjadi penulis terverifikasi.');
        }

        // Cek jika user sudah penulis tapi belum verified (bisa dari upgrade request atau manual)
        if ($user->role === 'penulis' && !$user->verified) {
            // Jika sudah ada pending request, redirect ke dashboard
            if ($user->verification_request_status === 'pending') {
                return redirect()->route('penulis.dashboard')
                    ->with('info', 'Permintaan verifikasi Anda sedang ditinjau. Mohon tunggu konfirmasi dari admin.');
            }
            // Jika sudah di-reject atau belum pernah request, redirect ke penulis dashboard
            // (bisa request verification dari penulis dashboard)
            return redirect()->route('penulis.dashboard')
                ->with('info', 'Anda sudah menjadi penulis. Silakan ajukan verifikasi untuk mendapatkan badge terverifikasi.');
        }

        // Cek jika user masih role 'user' tapi sudah memiliki pending upgrade request
        if ($user->role === 'user' && $user->verification_request_status === 'pending') {
            return redirect()->route('user.dashboard')
                ->with('info', 'Anda sudah memiliki permintaan upgrade yang sedang ditinjau. Mohon tunggu konfirmasi dari admin.');
        }

        // Hanya user dengan role 'user' yang bisa akses form upgrade request
        if ($user->role !== 'user') {
            return redirect()->back()->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
        }

        return view('user.upgrade-request');
    }

    public function submitUpgradeRequest(Request $request)
    {
        try {
            $request->validate([
                'verification_type' => 'required|in:perorangan,lembaga',
                'verification_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
                'bio' => 'required|string|max:1000',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'nullable|url',
                'location' => 'nullable|string|max:255',
                'social_links' => 'nullable|array',
            ]);

            $user = Auth::user();
            
            // Validasi: hanya user dengan role 'user' yang bisa submit upgrade request
            if ($user->role !== 'user') {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
            }

            // Validasi: cek jika user sudah memiliki pending request
            if ($user->verification_request_status === 'pending') {
                return redirect()->route('user.dashboard')
                    ->with('info', 'Anda sudah memiliki permintaan upgrade yang sedang ditinjau. Mohon tunggu konfirmasi dari admin.');
            }

            // Handle verification document upload
            $verificationDocumentPath = null;
            if ($request->hasFile('verification_document')) {
                // Delete old document if exists
                if ($user->verification_document && Storage::disk('public')->exists($user->verification_document)) {
                    Storage::disk('public')->delete($user->verification_document);
                }
                
                $verificationDocumentPath = $request->file('verification_document')->store('upgrade-documents', 'public');
            }

            // Update user: upgrade role ke penulis, set verification request status, dan simpan dokumen
            $user->update([
                'role' => 'penulis', // Upgrade role dari 'user' ke 'penulis'
                'verification_type' => $request->verification_type,
                'verification_document' => $verificationDocumentPath,
                'verification_requested_at' => now(), // Set timestamp request
                'verification_request_status' => 'pending', // Set status ke pending
                'verified' => false, // Pastikan verified masih false sampai admin approve
            ]);

            // Update or create profile
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

            // Log activity
            ActivityLogHelper::logUser('upgrade.requested', $user, "User {$user->name} mengajukan permintaan upgrade ke penulis");
            ActivityLogHelper::logSecurity('upgrade.requested', 'Permintaan upgrade ke penulis', [
                'user_id' => $user->id,
                'verification_type' => $request->verification_type
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Permintaan upgrade ke penulis berhasil dikirim! Admin akan meninjau permintaan Anda.');
                
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
