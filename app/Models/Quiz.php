<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['instructions', 'folder_id'];

    /* -------------- RELATIONSHIPS --------------*/
    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class); // All attempts for a quiz
    }

    public function pdfs()
    {
        return $this->hasMany(PdfResource::class);
    }

}
