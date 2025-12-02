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

        $user = Auth::user();

        if ($user->role_id == 1) {
            // Admin
            // return redirect()->intended(route('admin.profile.show', absolute: false));
            $full_name = trim($user->first_name . ' ' . $user->last_name);

            if ($full_name) {
                $message = 'Welcome back, ' . $full_name . '!';
            } else {
                $message = 'Welcome back!';
            }

            return redirect()
                ->intended(route('admin.profile.show', absolute: false))
                ->with('type', 'welcome')
                ->with('message', $message);
        } else {
            // Regular User / Student
            // return redirect()->intended(route('profile.show', absolute: false));
            return redirect()
            ->intended(route('profile.show', absolute: false))
            ->with('type', 'welcome')
            ->with('message', 'Welcome back, ' . $user->username . '!');
        }
    }

    /*
        I am using laravel breeze as my default login and registration system. I have 2 user roles: Admin, User. I am using Aphine JS to display the inputs using Toogleable Tabs. Now, I have an error with displaying the @error message since i have 2 password inputs. Is it possible to create a variable to store the role_is and then use it for condition?

        If role_id is 1, it should display the @error in teacher_password
        If role_id is 2, it should display the @error in teacher_password
    */


    /**
     * Destroy an authenticated session.
     */
    // public function destroy(Request $request): RedirectResponse
    // {
    //     Auth::guard('web')->logout();

    //     $request->session()->invalidate();

    //     $request->session()->regenerateToken();

    //     return redirect('/');
    // }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (empty($user->password)) {
            if ($user->role_id == 1) {
                return redirect()->route('admin.profile.show')
                        ->with('password', 'You cannot logout without setting a password.');
            } else{
                return redirect()->route('profile.show')
                        ->with('password', 'You cannot logout without setting a password.');
            }
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
