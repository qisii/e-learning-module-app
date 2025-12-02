<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoutPage extends Model
{
    protected $fillable = [
        'handout_id',
        'page_number',
    ];

    public function handout()
    {
        return $this->belongsTo(Handout::class);
    }

    public function components()
    {
        return $this->hasMany(HandoutComponent::class, 'page_id')->orderBy('sort_order');
    }

}
