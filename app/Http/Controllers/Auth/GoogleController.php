<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update existing user with Google info
                if (!$user->username) {
                    $user->username = User::generateUsername($user->name, $user->id);
                    $user->save();
                }
                $user->update([
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                ]);
            } else {
                // Create new user
                $username = User::generateUsername($googleUser->getName());
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'provider' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'role' => 'user',
                    'verified' => true, // Google users are considered verified
                    'password' => bcrypt(str()->random(16)), // Random password
                ]);
            }
            
            Auth::login($user);
            
            // Redirect user dan penulis ke home, admin dan editor ke dashboard
            if ($user->isAdmin() || $user->isEditor()) {
                return redirect()->intended('/dashboard');
            } else {
                return redirect()->intended('/');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}
