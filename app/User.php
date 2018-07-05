<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
