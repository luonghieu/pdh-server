<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InviteCode extends Model
{
    const DATE_STOP_INVITE_CODE = '2020-03-06';
    
    protected $fillable = [
        'code'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function histories()
    {
        return $this->hasMany(InviteCodeHistory::class);
    }
}
