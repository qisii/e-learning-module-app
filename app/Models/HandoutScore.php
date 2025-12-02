<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoutScore extends Model
{
    protected $table = 'handout_score';

    protected $fillable = [
        'handout_id',
        'score',
    ];

    public function handout(){
        return $this->belongsTo(Handout::class);
    }
}
