<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'role_id' => ['required', 'in:1,2'],
            'username' => ['nullable', 'string', 'max:20', 'unique:users,username'],
        ];

        // Conditional validation depending on role
        if ($request->role_id == 1) {
            // Teacher
            $rules['teacher_first_name'] = ['required', 'string', 'max:255'];
            $rules['teacher_last_name']  = ['required', 'string', 'max:255'];
            $rules['email'] = ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
            $rules['teacher_password'] = ['required', Rules\Password::defaults()];
        } else {
            // Student
            $rules['student_first_name'] = ['required', 'string', 'max:255'];
            $rules['student_last_name']  = ['required', 'string', 'max:255'];
            $rules['email'] = ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'];
            $rules['username'] = ['required', 'string', 'max:20', 'unique:users,username'];
            $rules['student_password'] = ['required', Rules\Password::defaults()];
        }

        // Custom error message
        $messages = [
            'student_first_name.required' => 'The first name field is required.',
            'teacher_first_name.required' => 'The first name field is required.',

            'student_last_name.required' => 'The last name field is required.',
            'teacher_last_name.required' => 'The last name field is required.',

            'student_password.required' => 'The password field is required.',
            'student_password.min' => 'The password must be at least 8 characters.',
            'teacher_password.required' => 'The password field is required.',
            'teacher_password.min' => 'The password must be at least 8 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'username.unique' => 'The username has already been taken.',
            'username.required' => 'The username is required.',
            'email.unique' => 'The email has already been taken.',
            'role_id.required' => 'Please select a role.',
        ];

        $validated = $request->validate($rules, $messages);

        // Assign variables based on role
        if ($request->role_id == 1) {
            $first_name = $validated['teacher_first_name'];
            $last_name  = $validated['teacher_last_name'];
            $password   = $validated['teacher_password'];
        } else {
            $first_name = $validated['student_first_name'];
            $last_name  = $validated['student_last_name'];
            $password   = $validated['student_password'];
        }

        // Create user
        $user = User::create([
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'username'   => $validated['username'] ?? null,
            'email'      => $validated['email'] ?? null,
            'password'   => Hash::make($password),
            'role_id'    => $validated['role_id'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        // Prepare message
        if ($user->role_id == 1) {
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

        } else {
            if ($user->username) {
                $message = "Welcome {$user->username}, Let's get started!";
            } else {
                $message = "Welcome, Let's get started!";
            }

            return redirect()
                ->route('profile.show')
                ->with('type', 'welcome')
                ->with('message', $message);
        }
    }
}
