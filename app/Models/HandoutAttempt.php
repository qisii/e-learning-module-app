<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoutAttempt extends Model
{
     protected $fillable = [
        'user_id',
        'handout_id',
        'time_spent',
        'attempt_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handout()
    {
        return $this->belongsTo(Handout::class);
    }
}
