<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['updated_at'];

    /* -------------- RELATIONSHIPS --------------*/
    
    // A project belongs to a user
    public function user(){
        return $this->belongsTo(User::class);
    }

    // A project has many folders
    public function folders(){
        return $this->hasMany(Folder::class)->orderBy('folder_type_id', 'asc');;
    }
}
