<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $fillable = ['question_id', 'choice_label_id', 'choice_text', 'is_correct'];

    /* -------------- RELATIONSHIPS --------------*/
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function getLabelAttribute()
    {
        // Converts 1 → A, 2 → B, etc.
        return chr(64 + $this->choice_label_id);
    }

}
