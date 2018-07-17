<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'reported_id',
        'user_id',
        'content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reported()
    {
        return $this->belongsTo(User::class);
    }
}
