<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfResource extends Model
{
    protected $fillable = [
        'folder_id',
        'handout_id',
        'quiz_id',
        'title',
        'gdrive_link',
    ];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function handout()
    {
        return $this->belongsTo(Handout::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
