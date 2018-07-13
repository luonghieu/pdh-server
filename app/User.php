<?php

namespace App;

use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

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

    public function isFavoritedUser($userId)
    {
        return $this->favorites()->pluck('users.id')->contains($userId);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'favorited_id')->withPivot('id', 'user_id', 'favorited_id', 'created_at', 'updated_at');
    }

    public function favoriters()
    {
        return $this->belongsToMany(User::class, 'favorites', 'favorited_id', 'user_id')->withPivot('id', 'user_id', 'favorited_id', 'created_at', 'updated_at');
    }

    public function avatars()
    {
        return $this->hasMany(Avatar::class)
            ->orderBy('is_default', 'desc');
    }

    public function prefecture()
    {
        return $this->belongsTo(Prefecture::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function bodyType()
    {
        return $this->belongsTo(BodyType::class);
    }
}
