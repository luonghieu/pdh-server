<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InviteCode extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function histories()
    {
        return $this->hasMany(InviteCodeHistory::class);
    }
}
