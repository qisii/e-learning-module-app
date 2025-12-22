<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentSuggestion extends Model
{
    public $table = 'comments_suggestions';

    public function user(){
        return $this->belongsTo(User::class);
    }
}
