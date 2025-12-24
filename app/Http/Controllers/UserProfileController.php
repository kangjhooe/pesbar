<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('name', 'like', '%' . str_replace('-', ' ', $username) . '%')->firstOrFail();
        
        if (!$user->isPenulis()) {
            abort(404);
        }

        $articles = $user->articles()
            ->where('status', 'published')
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('penulis.public-profile', compact('user', 'articles'));
    }

    public function upgradeRequest()
    {
        $user = Auth::user();
        
        if ($user->role !== 'user') {
            return redirect()->back()->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
        }

        return view('user.upgrade-request');
    }

    public function submitUpgradeRequest(Request $request)
    {
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
        
        if ($user->role !== 'user') {
            return redirect()->back()->with('error', 'Anda sudah memiliki role yang lebih tinggi!');
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

        // Update user with verification type and document
        $user->update([
            'verification_type' => $request->verification_type,
            'verification_document' => $verificationDocumentPath,
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

        return redirect()->route('dashboard')->with('success', 'Permintaan upgrade ke penulis berhasil dikirim! Admin akan meninjau permintaan Anda.');
    }
}
