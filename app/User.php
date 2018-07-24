<?php

namespace App;

use App\Enums\UserType;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
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

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function getAgeAttribute($value)
    {
        if ($this->date_of_birth) {
            return Carbon::parse($this->date_of_birth)->age;
        }
    }

    public function getPointAttribute($value)
    {
        if (!$value) {
            return 0;
        }

        return $value;
    }

    public function getCostAttribute($value)
    {
        if (!$value) {
            return 0;
        }

        return $value;
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

    public function getIsCastAttribute()
    {
        return UserType::CAST == $this->type;
    }

    public function getIsGuestAttribute()
    {
        return UserType::GUEST == $this->type;
    }

    public function getIsFavoritedAttribute()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();

        return $this->favoriters->contains($user->id) ? 1 : 0;
    }

    public function getIsBlockedAttribute()
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = Auth::user();

        return $this->blockers->contains($user->id) ? 1 : 0;
    }

    public function getBlocked($id)
    {
        return $this->blockers->contains($id) || $this->blocks->contains($id) ? 1 : 0;
    }

    public function getLastActiveAtAttribute($value)
    {
        return Cache::get('last_active_at_' . $this->id);
    }

    public function getLastActiveAttribute()
    {
        return latestOnlineStatus($this->last_active_at);
    }

    public function getIsOnlineAttribute($value)
    {
        $isOnline = Cache::get('is_online_' . $this->id);

        if (!$isOnline) {
            return 0;
        }

        if (now()->diffInMinutes($this->last_active_at) >= 2) {
            return 0;
        }

        return 1;
    }

    public function getRatingScoreAttribute()
    {
        return 1;
    }

    public function getRoomIdAttribute()
    {
        if (!Auth::check()) {
            return '';
        }

        $user = Auth::user();

        $room = $this->rooms()->direct()->whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->first();

        if (!$room) {
            return '';
        }

        return $room->id;
    }

    public function isFavoritedUser($userId)
    {
        return $this->favorites()->pluck('users.id')->contains($userId);
    }

    public function isBlockedUser($userId)
    {
        return $this->blocks()->pluck('users.id')->contains($userId);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites', 'user_id', 'favorited_id')
            ->withPivot('id', 'user_id', 'favorited_id', 'created_at', 'updated_at');
    }

    public function favoriters()
    {
        return $this->belongsToMany(User::class, 'favorites', 'favorited_id', 'user_id')
            ->withPivot('id', 'user_id', 'favorited_id', 'created_at', 'updated_at');
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

    public function blocks()
    {
        return $this->belongsToMany(User::class, 'blocks', 'user_id', 'blocked_id')
            ->withPivot('id', 'user_id', 'blocked_id', 'created_at', 'updated_at');
    }

    public function blockers()
    {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_id', 'user_id')
            ->withPivot('id', 'user_id', 'blocked_id', 'created_at', 'updated_at');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function reports()
    {
        return $this
            ->belongsToMany(User::class, 'reports', 'user_id', 'reported_id')
            ->withPivot('id', 'user_id', 'reported_id', 'content', 'created_at', 'updated_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
