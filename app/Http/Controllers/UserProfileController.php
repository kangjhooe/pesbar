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

        $data = [
            'user_id' => $user->id,
            'bio' => $request->bio,
            'website' => $request->website,
            'location' => $request->location,
            'social_links' => $request->social_links,
        ];

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Update atau create profile
        if ($user->profile) {
            $user->profile->update($data);
        } else {
            UserProfile::create($data);
        }

        return redirect()->route('dashboard')->with('success', 'Permintaan upgrade ke penulis berhasil dikirim! Admin akan meninjau permintaan Anda.');
    }
}
