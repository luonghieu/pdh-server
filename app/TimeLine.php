<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeLine extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function favorites()
    {
        return $this->hasMany(TimeLineFavorite::class);
    }
}
