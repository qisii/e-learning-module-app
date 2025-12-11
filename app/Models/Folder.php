<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['folder_type_id', 'project_id', 'updated_at'];

    /* -------------- RELATIONSHIPS --------------*/
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function handouts()
    {
        return $this->hasMany(Handout::class);
    }

    public function pdfResources()
    {
        return $this->hasMany(PdfResource::class);
    }

}
