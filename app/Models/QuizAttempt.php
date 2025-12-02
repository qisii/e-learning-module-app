<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'attempt_number',
        'score',
        'time_spent',
    ];

    // Each attempt belongs to a quiz
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    // Each attempt belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
