<?php

namespace App;

use App\Http\Resources\AvatarResource;
use Carbon\Carbon;
use App\Enums\UserType;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guarded = ['password_confirmation'];

    protected $fillable = [];

    protected $with = ['avatars'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAgeAttribute($value)
    {
        if ($this->date_of_birth) {
            return Carbon::parse($this->date_of_birth)->age;
        }
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    public function getIsAdminAttribute()
    {
        return UserType::ADMIN == $this->type;
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'favorited_id', 'id');
    }

    public function avatars()
    {
        return $this->hasMany(Avatar::class)
            ->orderBy('is_default', 'desc');
    }
}
