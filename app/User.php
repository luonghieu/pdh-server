<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    const TYPES = [
        'user' => 1,
        'cast' => 2,
        'admin' => 3,
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getIsAdminAttribute()
    {
        return self::TYPES['admin'] == $this->type;
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'favorited_id', 'id');
    }
}
