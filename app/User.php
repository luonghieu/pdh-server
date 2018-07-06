<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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

    protected $guarded = ['password_confirmation'];

    protected $fillable = [];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

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
