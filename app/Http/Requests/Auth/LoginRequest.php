<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // public function rules(): array
    // {
    //     return [
    //         'email' => ['required', 'string', 'email'],
    //         'password' => ['required', 'string'],
    //     ];
    // }
    public function rules(): array
    {
        // Different validation for teacher vs student
        if ($this->input('role_id') == 1) {
            // Teacher
            return [
                'email' => ['required', 'string', 'email'],
                'teacher_password' => ['required', 'string'],
            ];
        } else {
            // Student
            return [
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ];
        }
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function authenticate(): void
    // {
    //     $this->ensureIsNotRateLimited();

    //     if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
    //         RateLimiter::hit($this->throttleKey());

    //         throw ValidationException::withMessages([
    //             'email' => trans('auth.failed'),
    //         ]);
    //     }

    //     RateLimiter::clear($this->throttleKey());
    // }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = [];
        $role_id = $this->input('role_id');

        if ($role_id == 1) {
            // Teacher login uses email
            $credentials = [
                'email' => $this->input('email'),
                'password' => $this->input('teacher_password'),
                'role_id' => 1,
            ];
        } else {
            // Student login uses username
            $credentials = [
                'username' => $this->input('username'),
                'password' => $this->input('password'),
                'role_id' => 2,
            ];
        }

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $role_id == 1 ? 'email' : 'username' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function ensureIsNotRateLimited(): void
    // {
    //     if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
    //         return;
    //     }

    //     event(new Lockout($this));

    //     $seconds = RateLimiter::availableIn($this->throttleKey());

    //     throw ValidationException::withMessages([
    //         'email' => trans('auth.throttle', [
    //             'seconds' => $seconds,
    //             'minutes' => ceil($seconds / 60),
    //         ]),
    //     ]);
    // }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    // public function throttleKey(): string
    // {
    //     return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    // }
    public function throttleKey(): string
    {
        $keyField = $this->input('role_id') == 1 ? 'email' : 'username';
        return Str::transliterate(Str::lower($this->string($keyField)) . '|' . $this->ip());
    }
}
