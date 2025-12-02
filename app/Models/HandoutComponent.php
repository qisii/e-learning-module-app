<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandoutComponent extends Model
{
    protected $fillable = [
        'page_id',
        'type',
        'data',
        'sort_order',
    ];

    protected $casts = [
        'data' => 'array', // automatically convert JSON <-> array
    ];

    public function page()
    {
        return $this->belongsTo(HandoutPage::class, 'page_id');
    }
}
