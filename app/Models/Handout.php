<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Handout extends Model
{
    protected $fillable = [
        'title',
        'folder_id',
        'level_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function pages()
    {
        return $this->hasMany(HandoutPage::class)->orderBy('page_number');
    }

    public function score(){
        return $this->hasOne(HandoutScore::class);
    }

    public function pdfs()
    {
        return $this->hasMany(PdfResource::class);
    }

}
