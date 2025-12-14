<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ADMIN_ROLE_ID = 1;
    const USER_ROLE_ID = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'role_id',
        'google_id',
        'google_token',
        'google_refresh_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* -------------- RELATIONSHIPS --------------*/

    // A user can creare many projects
    public function projects(){
        return $this->hasMany(Project::class);
    }

    // A user has many quiz attempts
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class); // All attempts by a user
    }

    public function handoutAttempts()
    {
        return $this->hasMany(HandoutAttempt::class, 'user_id', 'id');
    }
}
