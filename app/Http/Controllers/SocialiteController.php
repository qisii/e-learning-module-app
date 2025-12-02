<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * This function will redirect to Google
     * NA parameters
     * Void return
     */
    public function googleLogin(){
        return Socialite::driver('google')->redirect();
    }

    /**
     * This function will authenticate the user through the Google Account
     */
    public function googleAuthentication(){
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if user exists
        // $userExists = User::where('google_id', $googleUser->id)->exists();
        // Check if user already exists
        $existingUser = User::where('google_id', $googleUser->id)->first();

        if ($existingUser) {
            // Keep existing first_name and last_name
            $firstname = $existingUser->first_name;
            $lastname  = $existingUser->last_name;
        } else {
            // For new users, set them as null (or default empty string if you prefer)
            $firstname = null;
            $lastname  = null;
        }

        // Create or update user
        $user = User::updateOrCreate(
            ['google_id' => $googleUser->id],
            [
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $googleUser->email,
                'role_id' => 1,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken ?? null,
            ]
        );

        Auth::login($user);

        if ($existingUser) {
            $full_name = trim($user->first_name . ' ' . $user->last_name);

            if ($full_name) {
                $message = 'Welcome back, ' . $full_name . '!';
            } else {
                $message = 'Welcome back!';
            }

            return redirect()
                ->route('admin.profile.show')
                ->with('type', 'welcome')
                ->with('message', $message);
        } else {
            $full_name = trim($user->first_name . ' ' . $user->last_name);

            if ($full_name) {
                $message = "Welcome {$full_name}, Let's get started!";
            } else {
                $message = "Welcome, Let's get started!";
            }

            return redirect()
                ->route('admin.profile.show')
                ->with('type', 'welcome')
                ->with('message', $message);
        }

        // return redirect()->route('admin.profile.show');
    }
}